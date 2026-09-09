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
    public function buildContext(AgentConversation $conversation, int $limit = 10): array
    {
        // Pega somente as últimas N mensagens enviadas. O resto fica pra trás.
        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->reverse()
            ->values();

        $history = [];

        // Injeção de "Memória de Longo Prazo".
        // Se a conversa for tão antiga que geramos um resumo (ex: "Vocês falaram sobre inadimplência antes"),
        // injetamos isso silenciosamente como instrução System.
        if ($conversation->summary !== null) {
            $history[] = [
                'role' => 'system',
                'content' => "MEMÓRIA DA CONVERSA ANTIGA: " . $conversation->summary
            ];
        }

        foreach ($messages as $msg) {
            $formattedMsg = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];

            if ($msg->tool_calls) {
                $formattedMsg['tool_calls'] = $msg->tool_calls;
            }
            if ($msg->tool_call_id) {
                $formattedMsg['tool_call_id'] = $msg->tool_call_id;
                // Para respostas de ferramenta, alguns LLMs pedem role="tool" e sem name, 
                // já o formato OpenAI e Deepseek exigem que seja role tool com id
                $formattedMsg['role'] = 'tool';
            }

            $history[] = $formattedMsg;
        }

        return $history;
    }
}
