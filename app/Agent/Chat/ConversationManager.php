<?php

declare(strict_types=1);

namespace App\Agent\Chat;

use App\Models\AgentConversation;
use App\Models\AgentMessage;

class ConversationManager
{
    /**
     * Adiciona uma nova mensagem ao banco e audita tokens.
     */
    public function addMessage(
        AgentConversation $conversation,
        string $role,
        ?string $content,
        ?array $toolCalls = null,
        ?string $toolCallId = null,
        int $tokens = 0,
        float $cost = 0.0
    ): AgentMessage {
        $msg = $conversation->messages()->create([
            'role' => $role,
            'content' => $content,
            'tool_calls' => $toolCalls,
            'tool_call_id' => $toolCallId,
            'tokens' => $tokens,
            'cost' => $cost,
        ]);

        // Controle de desgaste da sessão
        $conversation->increment('accumulated_tokens', $tokens);
        $conversation->increment('last_turn');

        return $msg;
    }

    /**
     * Compila as memórias passadas do Chat e formata estritamente no padrão API da LLM.
     * Implementa a técnica "Sliding Window" para evitar pagar caro por histórico velho.
     */
    public function buildContext(AgentConversation $conversation, int $limit = 20): array
    {
        // Pega somente as últimas N mensagens enviadas. O resto fica pra trás.
        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->reverse()
            ->values();

        // Pass 1: normalizar cada msg em array no formato da API.
        $items = [];
        foreach ($messages as $msg) {
            $item = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
            if ($msg->tool_calls) {
                $item['tool_calls'] = $msg->tool_calls;
            }
            if ($msg->tool_call_id) {
                $item['tool_call_id'] = $msg->tool_call_id;
                $item['role'] = 'tool';
            }
            $items[] = $item;
        }

        // Pass 2: filtrar blocos tool inválidos para o formato OpenAI/DeepSeek.
        $safe = self::filterInvalidToolBlocks($items);

        // Prefixa com memoria de longo prazo (se houver)
        $history = [];
        if ($conversation->summary !== null) {
            $history[] = [
                'role' => 'system',
                'content' => "MEMÓRIA DA CONVERSA ANTIGA: " . $conversation->summary,
            ];
        }

        return array_merge($history, $safe);
    }

    /**
     * Garante que a sequência de mensagens obedece as regras da API OpenAI/DeepSeek:
     * - toda mensagem role=tool precisa estar imediatamente após um assistant com tool_calls
     * - todo assistant com tool_calls precisa ter uma tool response para cada tool_call.id
     *   nas mensagens seguintes (senão o bloco inteiro é descartado)
     *
     * Público e static para viabilizar testes unitários sem tocar em DB.
     */
    public static function filterInvalidToolBlocks(array $items): array
    {
        $safe = [];
        $i = 0;
        $count = count($items);

        while ($i < $count) {
            $item = $items[$i];

            if ($item['role'] === 'tool') {
                $last = end($safe);
                if (!$last || $last['role'] !== 'assistant' || empty($last['tool_calls'] ?? null)) {
                    $i++;
                    continue;
                }
            }

            if ($item['role'] === 'assistant' && !empty($item['tool_calls'] ?? null)) {
                $requiredIds = array_values(array_filter(array_map(
                    fn ($tc) => $tc['id'] ?? null,
                    $item['tool_calls']
                )));

                $j = $i + 1;
                $foundIds = [];
                while ($j < $count && $items[$j]['role'] === 'tool') {
                    $foundIds[] = $items[$j]['tool_call_id'] ?? null;
                    $j++;
                }

                $missing = array_diff($requiredIds, array_values(array_filter($foundIds)));
                if (!empty($missing)) {
                    $i = $j;
                    continue;
                }
            }

            $safe[] = $item;
            $i++;
        }

        return $safe;
    }
}
