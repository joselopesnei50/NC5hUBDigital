<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Models\Cliente;
use App\Models\User;
use App\Models\WhatsappBotProfile;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Models\WhatsappMessage;
use App\Services\WhatsappBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappBotTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsCliente(): array
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create([
            'razao_social' => 'Empresa Bot',
            'user_id' => $user->id,
        ]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());

        return [$user->fresh(), $cliente];
    }

    public function test_bot_config_renderiza_com_todos_segmentos()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $response = $this->get(route('customer.whatsapp.bot.index'));
        $response->assertOk();
        $response->assertSee('Qual é o seu negócio?');
        $response->assertSee('Lanchonete');
        $response->assertSee('Corretor de Imoveis');
        $response->assertSee('Oficina Mecanica');
    }

    public function test_bot_update_persiste_profile_e_training()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $response = $this->post(route('customer.whatsapp.bot.update'), [
            'segment' => 'lanchonete',
            'is_active' => 1,
            'business_name' => 'Lanchonete do Ze',
            'horario_atendimento' => 'Seg-Sab 18h-23h',
            'catalogo_texto' => 'X-Burger R$ 20',
            'faq_custom' => 'P: Entrega? R: Sim',
            'regras_proibicoes' => 'Nunca promete abaixo de 40min',
        ]);

        $response->assertRedirect(route('customer.whatsapp.bot.index'));
        $this->assertDatabaseHas('whatsapp_bot_profiles', [
            'cliente_id' => $cliente->id,
            'segment' => 'lanchonete',
            'is_active' => 1,
        ]);
        $this->assertDatabaseHas('whatsapp_bot_training', [
            'cliente_id' => $cliente->id,
            'business_name' => 'Lanchonete do Ze',
        ]);
    }

    public function test_bot_segmento_invalido_rejeitado()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $response = $this->post(route('customer.whatsapp.bot.update'), [
            'segment' => 'invalido_qualquer',
        ]);

        $response->assertSessionHasErrors('segment');
    }

    public function test_toggle_bot_do_chat_inverte_estado()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511111112222',
            'contact_phone' => '5511111112222',
            'is_bot_active' => true,
            'last_message_at' => now(),
        ]);

        $response = $this->post(route('customer.whatsapp.bot.toggle', $chat));
        $response->assertRedirect();

        $chat->refresh();
        $this->assertFalse((bool) $chat->is_bot_active);

        $this->post(route('customer.whatsapp.bot.toggle', $chat));
        $chat->refresh();
        $this->assertTrue((bool) $chat->is_bot_active);
    }

    public function test_toggle_bot_de_outro_cliente_404()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $outroUser = User::factory()->create(['role' => 'cliente']);
        $outroCliente = Cliente::create([
            'razao_social' => 'Outro',
            'user_id' => $outroUser->id,
        ]);
        $chat = WhatsappChat::create([
            'cliente_id' => $outroCliente->id,
            'wa_id' => '5500000000000',
            'contact_phone' => '5500000000000',
            'last_message_at' => now(),
        ]);

        $response = $this->post(route('customer.whatsapp.bot.toggle', $chat));
        $response->assertNotFound();
    }

    public function test_bot_service_nao_responde_se_profile_inativo()
    {
        [$user, $cliente] = $this->actingAsCliente();

        WhatsappBotProfile::create([
            'cliente_id' => $cliente->id,
            'segment' => 'lanchonete',
            'is_active' => false,
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511111113333',
            'contact_phone' => '5511111113333',
            'is_bot_active' => true,
            'last_message_at' => now(),
        ]);

        $service = app(WhatsappBotService::class);
        $reply = $service->generateReply($chat, 'oi, quero um lanche');

        $this->assertNull($reply);
    }

    public function test_bot_service_nao_responde_em_grupo()
    {
        [$user, $cliente] = $this->actingAsCliente();

        WhatsappBotProfile::create([
            'cliente_id' => $cliente->id,
            'segment' => 'lanchonete',
            'is_active' => true,
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '55-grupo',
            'is_group' => true,
            'is_bot_active' => true,
            'last_message_at' => now(),
        ]);

        $service = app(WhatsappBotService::class);
        $this->assertNull($service->generateReply($chat, 'alguma coisa'));
    }

    public function test_bot_service_escala_e_pausa_chat_quando_lead_pede_atendente()
    {
        [$user, $cliente] = $this->actingAsCliente();

        WhatsappBotProfile::create([
            'cliente_id' => $cliente->id,
            'segment' => 'lanchonete',
            'is_active' => true,
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999991111',
            'contact_phone' => '5511999991111',
            'is_bot_active' => true,
            'last_message_at' => now(),
        ]);

        $service = app(WhatsappBotService::class);
        $reply = $service->generateReply($chat, 'Quero falar com atendente humano, por favor');

        $this->assertNull($reply);
        $chat->refresh();
        $this->assertFalse((bool) $chat->is_bot_active);
    }
}
