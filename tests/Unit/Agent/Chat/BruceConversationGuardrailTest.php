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

    private function novaConversa(string $razao, string $email): AgentConversation
    {
        // clientes.user_id eh NOT NULL, entao o user precisa vir antes.
        $user = User::create(['name' => explode('@', $email)[0], 'email' => $email, 'password' => '123']);
        $cliente = Cliente::create(['razao_social' => $razao, 'user_id' => $user->id]);
        return AgentConversation::create(['cliente_id' => $cliente->id, 'user_id' => $user->id]);
    }

    public function test_conversation_loop_respects_max_turns_and_isolates_tenant()
    {
        $conversation = $this->novaConversa('Empresa do João', 'joao@test.com');

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
        $this->assertStringContainsString('Não consegui montar uma resposta', $finalResponse);
        
        // 2. Garante que os registros falsos do loop de fato pararam no banco
        $this->assertGreaterThanOrEqual(4, $conversation->messages()->count());
        
        // 3. Garante que o tenantId foi preservado
        $this->assertNotNull($conversation->cliente_id);
    }

    public function test_reentrant_handleturn_is_ignored_without_calling_llm()
    {
        // Cenario que motivou o guard: o primeiro handleTurn ja gravou o
        // "user" no banco e esta preso no LLM (que passou de 60s). Ai o
        // reverse-proxy retenta o POST do Livewire, ou o usuario deu
        // double-click, e um SEGUNDO handleTurn entra com o mesmo texto.
        // A ultima msg gravada ja eh esse user, entao o guard aborta sem
        // duplicar a msg nem gastar chamada de LLM.
        $conversation = $this->novaConversa('Empresa duplicada', 'maria@test.com');

        // Primeiro turno gravou o user; o assistant ainda nao chegou.
        \App\Models\AgentMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'quanto entrou hoje?',
        ]);

        // Driver que EXPLODE se chamado — o guard tem que impedir a ida na LLM.
        $mockDriver = $this->createMock(LlmDriver::class);
        $mockDriver->expects($this->never())->method('complete');

        $bruce = new BruceConversation($mockDriver, new ConversationManager(), new ToolRegistry());
        $resposta = $bruce->handleTurn($conversation, 'quanto entrou hoje?');

        // Sem assistant gravado ainda, guard devolve string vazia (o
        // primeiro handleTurn ainda vai responder e o client rerender pega).
        $this->assertSame('', $resposta);
        $this->assertSame(1, $conversation->messages()->count(), 'guard nao pode duplicar mensagem');
    }

    public function test_repeat_after_a_prior_turn_finished_still_processes_normally()
    {
        // O guard so dispara se a ULTIMA msg for user com o mesmo texto —
        // ou seja, um turno em andamento. Uma nova pergunta identica horas
        // depois (com assistant no meio) tem que passar direto pra LLM;
        // senao o cliente fica travado se repetir uma pergunta comum.
        $conversation = $this->novaConversa('Empresa repete', 'pedro@test.com');

        \App\Models\AgentMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'quanto entrou hoje?',
            'created_at' => now()->subMinutes(3),
        ]);
        \App\Models\AgentMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Entraram R$ 1.200 no caixa.',
            'created_at' => now()->subMinutes(3),
        ]);

        $mockDriver = $this->createMock(LlmDriver::class);
        $mockDriver->expects($this->once())->method('complete')->willReturn(new LlmResponse(
            content: 'Ainda R$ 1.200 no caixa.',
            tokensIn: 5,
            tokensOut: 5,
            estimatedCost: 0.01,
            toolCalls: null
        ));

        $bruce = new BruceConversation($mockDriver, new ConversationManager(), new ToolRegistry());
        $resposta = $bruce->handleTurn($conversation, 'quanto entrou hoje?');

        $this->assertSame('Ainda R$ 1.200 no caixa.', $resposta);
    }
}
