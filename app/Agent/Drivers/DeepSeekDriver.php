<?php

declare(strict_types=1);

namespace App\Agent\Drivers;

use App\Agent\Contracts\LlmDriver;
use App\Agent\DTOs\PromptPayload;
use App\Agent\DTOs\LlmResponse;
use App\Agent\Exceptions\LlmException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeepSeekDriver implements LlmDriver
{
    public function __construct(
        private string $apiKey,
        private string $baseUrl,
        private string $model,
        private float $costPer1kIn,
        private float $costPer1kOut,
        private int $timeout
    ) {
    }

    public function complete(PromptPayload $payload): LlmResponse
    {
        $messages = [
            ['role' => 'system', 'content' => $payload->systemPrompt],
        ];

        // Injeta os dados consolidados (O LLM só vê o que passarmos aqui)
        if ($payload->snapshot !== null) {
            $messages[] = [
                'role' => 'system',
                'content' => "SNAPSHOT DO NEGÓCIO:\n" . json_encode($payload->snapshot, JSON_UNESCAPED_UNICODE)
            ];
        }

        // Histórico de conversa (Para a Fase 2 - BruceIA)
        if ($payload->history !== null) {
            foreach ($payload->history as $msg) {
                $messages[] = $msg;
            }
        }

        // Só anexa o turno do usuário quando ele existe de fato.
        // No chat o userPrompt vem vazio: a fala já está na última mensagem do history.
        if (trim($payload->userPrompt) !== '') {
            $messages[] = ['role' => 'user', 'content' => $payload->userPrompt];
        }

        $requestData = [
            'model' => $this->model,
            'messages' => $messages,
        ];

        // Force json_object only if explicitly requested in prompt to avoid 400 errors or hallucinations in Chat
        if (stripos($payload->systemPrompt, 'SCHEMA JSON ABAIXO') !== false) {
            $requestData['response_format'] = ['type' => 'json_object'];
        }

        if ($payload->tools !== null && $payload->toolChoice !== 'none') {
            $requestData['tools'] = $payload->tools;
            unset($requestData['response_format']);

            if ($payload->toolChoice !== null) {
                $requestData['tool_choice'] = $payload->toolChoice;
            }
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->withOptions(['curl' => [\CURLOPT_IPRESOLVE => \CURL_IPRESOLVE_V4]])
                ->baseUrl($this->baseUrl)
                ->timeout($this->timeout)
                ->retry(2, 1000) // Tenta novamente 2 vezes caso a API de IA engasgue (backoff de 1 seg)
                ->post('/chat/completions', $requestData);

            if ($response->failed()) {
                Log::error('[DeepSeekDriver] Erro da API', ['status' => $response->status(), 'body' => $response->body()]);
                throw new LlmException('Falha ao comunicar com o modelo de inteligência artificial.');
            }

            $data = $response->json();

            $content = $data['choices'][0]['message']['content'] ?? '';
            $toolCalls = $data['choices'][0]['message']['tool_calls'] ?? null;
            
            $tokensIn = $data['usage']['prompt_tokens'] ?? 0;
            $tokensOut = $data['usage']['completion_tokens'] ?? 0;

            // Lógica financeira: Contabiliza o custo exato da requisição em centavos de Dólar/Real
            $cost = (($tokensIn / 1000) * $this->costPer1kIn) + (($tokensOut / 1000) * $this->costPer1kOut);

            return new LlmResponse(
                content: $content,
                tokensIn: $tokensIn,
                tokensOut: $tokensOut,
                estimatedCost: $cost,
                toolCalls: $toolCalls
            );

        } catch (Throwable $e) {
            Log::error('[DeepSeekDriver] Exceção crítica', ['message' => $e->getMessage()]);
            throw new LlmException('Erro interno na geração via LLM: ' . $e->getMessage());
        }
    }
}
