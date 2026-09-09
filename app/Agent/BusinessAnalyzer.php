<?php

declare(strict_types=1);

namespace App\Agent;

use App\Agent\Contracts\LlmDriver;
use App\Agent\DTOs\PromptPayload;
use Exception;
use Illuminate\Support\Facades\Log;

class BusinessAnalyzer
{
    public function __construct(
        private readonly LlmDriver $driver,
        private readonly SnapshotBuilder $snapshotBuilder,
        private readonly ResponseValidator $validator
    ) {
    }

    /**
     * Orquestra a geração da análise, aplicando retentativas caso o LLM alucine o formato.
     */
    public function analyze(int $tenantId, int $periodDays = 30): array
    {
        // 1. Gera o retrato (snapshot) dos números do Tenant
        $snapshot = $this->snapshotBuilder->build($tenantId, $periodDays);

        // 2. Constrói o Prompt do Sistema injetando o contrato do Schema JSON esperado
        $schemaExigido = [
            'diagnostico_geral' => 'Sua leitura macro do momento em 1 paragrafo (texto).',
            'insights' => [
                [
                    'titulo' => 'O que está acontecendo',
                    'categoria' => 'enum: pedidos, caixa, clientes, produtos',
                    'severidade' => 'enum: info, atencao, critico',
                    'evidencia' => ['chave_da_metrica_do_snapshot' => 'valor_encontrado_no_snapshot'],
                    'acao_sugerida' => 'Recomendacao pratica e acionavel.'
                ]
            ]
        ];

        $systemPrompt = config('agent.prompts.system_analysis') . "\n\n" .
            "VOCÊ ESTÁ PROIBIDO DE RETORNAR TEXTO LIVRE. SEU RETORNO DEVE SER ESTRITAMENTE O SCHEMA JSON ABAIXO:\n" .
            json_encode($schemaExigido, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $payload = new PromptPayload(
            systemPrompt: $systemPrompt,
            userPrompt: 'Gere a análise gerencial com base no snapshot atual informado.',
            snapshot: $snapshot
        );

        $attempts = 0;
        $maxAttempts = 2; // Tentativa normal + 1 Retentativa de Correção
        $lastError = '';

        $totalCost = 0.0;
        $totalTokensIn = 0;
        $totalTokensOut = 0;

        while ($attempts < $maxAttempts) {
            $attempts++;

            try {
                // Chama a IA
                $response = $this->driver->complete($payload);
                
                $totalCost += $response->estimatedCost;
                $totalTokensIn += $response->tokensIn;
                $totalTokensOut += $response->tokensOut;

                // Valida o retorno rigorosamente
                $validatedData = $this->validator->validate($response->content);

                return [
                    'success' => true,
                    'data' => $validatedData,
                    'cost' => $totalCost,
                    'tokens_in' => $totalTokensIn,
                    'tokens_out' => $totalTokensOut,
                    'snapshot' => $snapshot
                ];

            } catch (Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("[BusinessAnalyzer] Alucinação de formato na tentativa {$attempts}: {$lastError}");
                
                // Feedback Loop: Diz ao LLM o que ele errou na rodada anterior para ele arrumar
                $payload = new PromptPayload(
                    systemPrompt: $systemPrompt,
                    userPrompt: "Sua resposta anterior falhou na nossa validação sistêmica com o seguinte erro de engenharia: '{$lastError}'. Por favor, revise sua saída anterior e retorne APENAS um JSON válido consertando este problema.",
                    snapshot: clone $snapshot // Reenvia o snapshot para ele não esquecer o contexto numérico
                );
            }
        }

        // Se o laço (while) acabar, significa que o LLM não soube formular o JSON mesmo com 2 chances
        return [
            'success' => false,
            'error' => 'Falha severa após retentativas: O modelo falhou em obedecer o schema JSON. (' . $lastError . ')',
            'snapshot' => $snapshot,
            'cost' => $totalCost,
            'tokens_in' => $totalTokensIn,
            'tokens_out' => $totalTokensOut,
        ];
    }
}
