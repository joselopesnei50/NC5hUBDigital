<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\RunBusinessAnalysis;
use App\Models\Cliente;
use Illuminate\Console\Command;

class AnalyzeBusinessCommand extends Command
{
    protected $signature = 'agent:analyze {tenant? : O ID do cliente específico} {--period=30 : Número de dias para analisar}';
    protected $description = 'Dispara o Agente de Análise de Negócios via LLM para os clientes (Tenants)';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $period = (int) $this->option('period');

        // Execução Sob Demanda (Para um único cliente, via painel ou botão)
        if ($tenantId) {
            $this->info("Despachando análise avulsa para o Tenant ID: {$tenantId} (Período: {$period} dias)...");
            RunBusinessAnalysis::dispatch((int) $tenantId, $period, 'on_demand');
            $this->info('Análise enfileirada com sucesso na queue "agent".');
            return self::SUCCESS;
        }

        // Execução Agendada em Massa (Scheduler Semanal)
        $this->info('Iniciando análise periódica para todos os Tenants ativos...');
        
        $clientes = Cliente::select('id')->get();
        
        foreach ($clientes as $cliente) {
            // Joga para a fila. O Queue Worker do Laravel (Supervisor) vai processar um por vez, evitando rate limit
            RunBusinessAnalysis::dispatch($cliente->id, $period, 'scheduled');
        }

        $this->info("Foram agendadas análises para {$clientes->count()} tenants.");
        return self::SUCCESS;
    }
}
