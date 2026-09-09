<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Agent\BusinessAnalyzer;
use App\Agent\Enums\AgentRunStatus;
use App\Events\AnalysisCompleted;
use App\Models\AgentInsight;
use App\Models\AgentRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunBusinessAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Retentativas nativas da Fila do Laravel caso o LLM caia por problema de rede
    public int $tries = 2;
    
    // Timeout elevado (5 minutos) pois APIs de Chat podem demorar na leitura de JSONs grandes
    public int $timeout = 300;

    public function __construct(
        public int $tenantId,
        public int $periodDays,
        public string $type = 'scheduled'
    ) {
        $this->onQueue('agent');
    }

    public function handle(BusinessAnalyzer $analyzer): void
    {
        // === CONTROLE DE IDEMPOTÊNCIA ===
        // Previne que o agente rode 2x para o mesmo tenant no mesmo período se houver dupla-clicagem ou lag.
        $recentRun = AgentRun::where('cliente_id', $this->tenantId)
            ->where('period_days', $this->periodDays)
            ->where('type', $this->type)
            ->whereIn('status', [AgentRunStatus::PROCESSING, AgentRunStatus::COMPLETED])
            ->where('created_at', '>=', now()->subHours(12))
            ->exists();

        if ($recentRun) {
            Log::info("[RunBusinessAnalysis] Análise ignorada para Tenant {$this->tenantId}: Já existe rodada recente.");
            return;
        }

        // Abre o registro de execução
        $run = AgentRun::create([
            'cliente_id' => $this->tenantId,
            'type' => $this->type,
            'status' => AgentRunStatus::PROCESSING,
            'period_days' => $this->periodDays,
        ]);

        $startTime = microtime(true);

        try {
            $result = $analyzer->analyze($this->tenantId, $this->periodDays);
            $duration = microtime(true) - $startTime;

            $run->update([
                'snapshot' => $result['snapshot'] ?? null,
                'tokens_in' => $result['tokens_in'] ?? 0,
                'tokens_out' => $result['tokens_out'] ?? 0,
                'cost' => $result['cost'] ?? 0,
                'duration_seconds' => $duration,
            ]);

            if ($result['success']) {
                $run->update(['status' => AgentRunStatus::COMPLETED]);

                // Grava os Insights explodidos na tabela relacional para facilitar queries da View
                foreach ($result['data']['insights'] as $insightData) {
                    AgentInsight::create([
                        'agent_run_id' => $run->id,
                        'cliente_id' => $this->tenantId,
                        'category' => $insightData['categoria'],
                        'severity' => $insightData['severidade'],
                        'title' => $insightData['titulo'],
                        'body' => $result['data']['diagnostico_geral'], // Usado como preâmbulo se necessário
                        'evidence' => $insightData['evidencia'],
                        'suggested_action' => $insightData['acao_sugerida'],
                        'status' => 'novo'
                    ]);
                }

                // Dispara o disparo visual para o front-end
                event(new AnalysisCompleted($run));
            } else {
                // Modo Degradado: O LLM falhou em gerar texto, mas os números (snapshot) foram salvos
                $run->update([
                    'status' => AgentRunStatus::DEGRADED,
                    'error' => $result['error']
                ]);
            }

        } catch (Throwable $e) {
            // Em caso de falha fatal (banco caiu, erro de código interno)
            $run->update([
                'status' => AgentRunStatus::FAILED,
                'error' => $e->getMessage(),
                'duration_seconds' => microtime(true) - $startTime,
            ]);
            
            // Repassa o erro pro Queue Manager tentar a segunda chance configurada em $tries
            throw $e; 
        }
    }
}
