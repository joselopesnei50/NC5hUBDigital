<?php

declare(strict_types=1);

namespace App\Agent\Drivers;

use App\Agent\Contracts\LlmDriver;
use App\Agent\DTOs\PromptPayload;
use App\Agent\DTOs\LlmResponse;

class NullDriver implements LlmDriver
{
    /**
     * Driver fantasma. Não faz requisições externas e devolve uma fixture segura.
     * Ideal para rodar testes automatizados sem gastar créditos de API.
     */
    public function complete(PromptPayload $payload): LlmResponse
    {
        $dummyContent = json_encode([
            'insights' => [
                [
                    'titulo' => 'Insight de Teste Local',
                    'categoria' => 'caixa',
                    'severidade' => 'info',
                    'evidencia' => 'N/A',
                    'acao_sugerida' => 'Apenas para ambiente de desenvolvimento.'
                ]
            ]
        ]);

        return new LlmResponse(
            content: $dummyContent,
            tokensIn: 100,
            tokensOut: 50,
            estimatedCost: 0.0000,
            toolCalls: null
        );
    }
}
