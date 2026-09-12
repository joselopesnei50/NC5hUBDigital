<?php

declare(strict_types=1);

namespace Tests\Unit\Agent\Chat;

use App\Agent\Chat\ConversationManager;
use Tests\TestCase;

class ConversationManagerFilterTest extends TestCase
{
    /** Helpers para montar mensagens no formato interno usado pelo filtro. */
    private function u(string $c): array
    {
        return ['role' => 'user', 'content' => $c];
    }

    private function a(string $c): array
    {
        return ['role' => 'assistant', 'content' => $c];
    }

    private function atc(array $ids): array
    {
        return [
            'role' => 'assistant',
            'content' => null,
            'tool_calls' => array_map(
                fn ($id) => ['id' => $id, 'function' => ['name' => 't', 'arguments' => '{}']],
                $ids
            ),
        ];
    }

    private function t(string $id, string $content = '{}'): array
    {
        return ['role' => 'tool', 'content' => $content, 'tool_call_id' => $id];
    }

    public function test_multi_tool_block_survives_intact()
    {
        // Cenario que quebrava antes: assistant pede 2 tool_calls e o filtro
        // descartava a 2a tool response porque end($safe) era a 1a tool
        // (nao assistant), invalidando o par inteiro.
        $items = [
            $this->u('faz uma analise'),
            $this->atc(['a1', 'a2']),
            $this->t('a1', '{"receita":100}'),
            $this->t('a2', '{"clientes":10}'),
        ];

        $result = ConversationManager::filterInvalidToolBlocks($items);

        $this->assertCount(4, $result, 'Todo o bloco multi-tool deve sobreviver');
        $this->assertSame('user', $result[0]['role']);
        $this->assertSame('assistant', $result[1]['role']);
        $this->assertSame(['a1', 'a2'], array_column($result[1]['tool_calls'], 'id'));
        $this->assertSame('tool', $result[2]['role']);
        $this->assertSame('a1', $result[2]['tool_call_id']);
        $this->assertSame('tool', $result[3]['role']);
        $this->assertSame('a2', $result[3]['tool_call_id']);
    }

    public function test_orphan_tool_at_start_is_dropped()
    {
        $items = [
            $this->t('X', 'restou de round anterior'),
            $this->u('pergunta nova'),
        ];

        $result = ConversationManager::filterInvalidToolBlocks($items);

        $this->assertCount(1, $result);
        $this->assertSame('user', $result[0]['role']);
    }

    public function test_assistant_tool_calls_without_response_is_dropped()
    {
        // Bloco quebrado no meio + rodada boa depois: o quebrado sai, o bom fica.
        $items = [
            $this->u('caixa?'),
            $this->atc(['BROKEN']),
            $this->u('esqueca, e clientes?'),
            $this->atc(['B']),
            $this->t('B'),
            $this->a('resposta boa'),
        ];

        $result = ConversationManager::filterInvalidToolBlocks($items);

        $this->assertCount(5, $result);
        $this->assertEmpty(
            $result[1]['tool_calls'] ?? null,
            'O assistant BROKEN nao deveria ter sobrevivido'
        );
    }

    public function test_partial_responses_to_multi_tool_assistant_drop_whole_block()
    {
        // 2 tool_calls, so 1 resposta: nao ha como remendar, o bloco todo sai.
        $items = [
            $this->u('analisa'),
            $this->atc(['X', 'Y']),
            $this->t('X'),
            $this->u('e?'),
        ];

        $result = ConversationManager::filterInvalidToolBlocks($items);

        $this->assertCount(2, $result);
        foreach ($result as $msg) {
            $this->assertSame('user', $msg['role']);
        }
    }
}
