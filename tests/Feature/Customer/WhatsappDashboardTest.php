<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Models\Cliente;
use App\Models\ClienteFinal;
use App\Models\PedidoCliente;
use App\Models\User;
use App\Models\WhatsappBotInteraction;
use App\Models\WhatsappChat;
use App\Models\WhatsappMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsCliente(): array
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create([
            'razao_social' => 'Empresa Dashboard',
            'user_id' => $user->id,
        ]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());

        return [$user->fresh(), $cliente];
    }

    public function test_dashboard_renderiza_vazio()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $response = $this->get(route('customer.whatsapp.dashboard'));
        $response->assertOk();
        $response->assertSee('Dashboard do Bot IA');
        $response->assertSee('Sem interações no período');
    }

    public function test_dashboard_calcula_kpis_com_dados()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999991111',
            'contact_phone' => '5511999991111',
            'last_message_at' => now(),
        ]);

        for ($i = 0; $i < 5; $i++) {
            WhatsappMessage::create([
                'cliente_id' => $cliente->id,
                'chat_id' => $chat->id,
                'direction' => 'inbound',
                'type' => 'chat',
                'content' => "msg {$i}",
                'status' => 'delivered',
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            WhatsappBotInteraction::create([
                'cliente_id' => $cliente->id,
                'chat_id' => $chat->id,
                'tokens_in' => 100,
                'tokens_out' => 50,
                'cost' => 0.0015,
                'response_time_ms' => 1200,
                'escalated' => false,
                'failed' => false,
            ]);
        }

        WhatsappBotInteraction::create([
            'cliente_id' => $cliente->id,
            'chat_id' => $chat->id,
            'escalated' => true,
            'failed' => false,
        ]);

        $response = $this->get(route('customer.whatsapp.dashboard'));
        $response->assertOk();
        $response->assertSee('5', false);
        $response->assertSee('60,0%');
        $response->assertDontSee('Sem interações no período');
    }

    public function test_dashboard_toggle_30d()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $response = $this->get(route('customer.whatsapp.dashboard', ['range' => '30d']));
        $response->assertOk();
        $response->assertSee('últimos 30 dias');
    }

    public function test_dashboard_isola_por_cliente()
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
        WhatsappBotInteraction::create([
            'cliente_id' => $outroCliente->id,
            'chat_id' => $chatOutro->id,
            'cost' => 999,
            'escalated' => false,
            'failed' => false,
        ]);

        $response = $this->get(route('customer.whatsapp.dashboard'));
        $response->assertOk();
        $response->assertDontSee('999', false);
    }

    public function test_dashboard_conta_pedidos_gerados_no_periodo()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $cf = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'Teste',
            'nome_responsavel' => 'Fulano',
            'telefone' => '5511999992222',
        ]);
        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'wa_id' => '5511999992222',
            'contact_phone' => '5511999992222',
            'last_message_at' => now(),
        ]);
        WhatsappBotInteraction::create([
            'cliente_id' => $cliente->id,
            'chat_id' => $chat->id,
            'escalated' => false,
            'failed' => false,
        ]);
        PedidoCliente::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'titulo' => 'Pedido via bot',
            'valor' => 100,
            'status' => 'Em Andamento',
            'data_pedido' => now(),
        ]);

        $response = $this->get(route('customer.whatsapp.dashboard'));
        $response->assertOk();
        $response->assertSeeInOrder(['Pedidos gerados', '1']);
    }
}
