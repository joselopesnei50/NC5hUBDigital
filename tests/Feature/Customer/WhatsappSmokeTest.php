<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Models\Cliente;
use App\Models\User;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use App\Models\WhatsappMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsCliente(): array
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create([
            'id' => 1,
            'razao_social' => 'Empresa Teste',
            'user_id' => $user->id,
        ]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());

        return [$user->fresh(), $cliente];
    }

    public function test_conexao_renderiza_sem_instancia()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $response = $this->get(route('customer.whatsapp.conexao'));
        $response->assertOk();
        $response->assertSee('WhatsApp não conectado');
    }

    public function test_conexao_renderiza_com_instancia()
    {
        [$user, $cliente] = $this->actingAsCliente();

        WhatsappInstance::create([
            'cliente_id' => $cliente->id,
            'instance_name' => 'test-inst',
            'instance_token' => str_repeat('a', 64),
            'status' => 'open',
            'phone_number' => '5511999999999',
            'connected_at' => now(),
        ]);

        $response = $this->get(route('customer.whatsapp.conexao'));
        $response->assertOk();
        $response->assertSee('Conectado');
        $response->assertSee('5511999999999');
    }

    public function test_inbox_renderiza_vazio()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $response = $this->get(route('customer.whatsapp.inbox'));
        $response->assertOk();
        $response->assertSee('Nenhuma conversa');
    }

    public function test_inbox_lista_chats()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511888887777',
            'contact_name' => 'Fulano',
            'contact_phone' => '5511888887777',
            'last_message_at' => now(),
        ]);

        WhatsappMessage::create([
            'cliente_id' => $cliente->id,
            'chat_id' => $chat->id,
            'direction' => 'inbound',
            'type' => 'chat',
            'content' => 'Olá, teste!',
            'status' => 'delivered',
        ]);

        $response = $this->get(route('customer.whatsapp.inbox'));
        $response->assertOk();
        $response->assertSee('Fulano');
        $response->assertSee('Olá, teste!');
    }

    public function test_chat_isola_por_cliente()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $outroUser = User::factory()->create(['role' => 'cliente']);
        $outroCliente = Cliente::create([
            'razao_social' => 'Outro',
            'user_id' => $outroUser->id,
        ]);

        $chatOutro = WhatsappChat::create([
            'cliente_id' => $outroCliente->id,
            'wa_id' => '5500000000000',
            'contact_phone' => '5500000000000',
            'last_message_at' => now(),
        ]);

        $response = $this->get(route('customer.whatsapp.chat', $chatOutro));
        $response->assertNotFound();
    }

    public function test_webhook_rejeita_token_invalido()
    {
        $response = $this->postJson('/api/webhooks/evolution/token-que-nao-existe', [
            'event' => 'MESSAGES_UPSERT',
            'data' => [],
        ]);
        $response->assertOk();
        $response->assertJson(['status' => 'ignored']);
    }

    public function test_webhook_processa_mensagem_inbound()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create([
            'razao_social' => 'Empresa X',
            'user_id' => $user->id,
        ]);

        $instance = WhatsappInstance::create([
            'cliente_id' => $cliente->id,
            'instance_name' => 'nc5-test',
            'instance_token' => 'tok-abc-123',
            'status' => 'open',
        ]);

        $response = $this->postJson('/api/webhooks/evolution/tok-abc-123', [
            'event' => 'MESSAGES_UPSERT',
            'data' => [
                'key' => [
                    'id' => 'wa-msg-001',
                    'fromMe' => false,
                    'remoteJid' => '5511987654321@s.whatsapp.net',
                ],
                'pushName' => 'Fulano Teste',
                'messageType' => 'conversation',
                'message' => ['conversation' => 'Oi, quero fazer um pedido'],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('whatsapp_chats', [
            'cliente_id' => $cliente->id,
            'wa_id' => '5511987654321',
            'contact_name' => 'Fulano Teste',
        ]);
        $this->assertDatabaseHas('whatsapp_messages', [
            'cliente_id' => $cliente->id,
            'wa_message_id' => 'wa-msg-001',
            'direction' => 'inbound',
            'content' => 'Oi, quero fazer um pedido',
        ]);
    }
}
