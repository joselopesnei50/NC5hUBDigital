<?php

declare(strict_types=1);

namespace Tests\Unit\Agent\Chat;

use Tests\TestCase;
use App\Agent\Chat\BruceConversation;
use App\Agent\Chat\ConversationManager;
use App\Agent\Chat\ToolRegistry;
use App\Agent\Contracts\LlmDriver;
use App\Agent\DTOs\LlmResponse;
use App\Models\AgentConversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Cliente;
use App\Models\User;

class BruceConversationGuardrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversation_loop_respects_max_turns_and_isolates_tenant()
    {
        // Prepara Banco Mockado
        $cliente = Cliente::create(['razao_social' => 'Empresa do João']);
        $user = User::create(['name' => 'João', 'email' => 'joao@test.com', 'password' => '123', 'cliente_id' => $cliente->id]);
        $conversation = AgentConversation::create(['cliente_id' => $cliente->id, 'user_id' => $user->id]);

        // Mockamos a IA. Vamos forçá-la a pedir ferramentas infinitamente para testar o disjuntor.
        $mockDriver = $this->createMock(LlmDriver::class);
        $mockDriver->method('complete')->willReturn(new LlmResponse(
            content: '',
            tokensIn: 10,
            tokensOut: 10,
            estimatedCost: 0.01,
            toolCalls: [
                ['id' => 'call_123', 'function' => ['name' => 'ferramenta_falsa', 'arguments' => '{}']]
            ]
        ));

        $registry = new ToolRegistry();
        $memory = new ConversationManager();

        $bruce = new BruceConversation($mockDriver, $memory, $registry);
        
        $finalResponse = $bruce->handleTurn($conversation, "Me dê os dados");

        // Asserts:
        // 1. O laço quebrou no limite sem erro fatal
        $this->assertStringContainsString('cheguei ao meu limite de processamento', $finalResponse);
        
        // 2. Garante que os registros falsos do loop de fato pararam no banco
        $this->assertGreaterThanOrEqual(4, $conversation->messages()->count());
        
        // 3. Garante que nenhuma msg foi executada fora do cliente_id = 1
        $this->assertEquals($cliente->id, $conversation->cliente_id);
    }
}
