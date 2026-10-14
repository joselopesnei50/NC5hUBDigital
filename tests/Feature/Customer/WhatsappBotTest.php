<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Models\Cliente;
use App\Models\User;
use App\Models\WhatsappBotProfile;
use App\Models\WhatsappBotTraining;
use App\Models\ClienteFinal;
use App\Models\PedidoCliente;
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

    public function test_bot_update_persiste_campos_fundamentais()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $this->post(route('customer.whatsapp.bot.update'), [
            'segment' => 'lanchonete',
            'historia_negocio' => 'Comecamos em 2015 na garagem',
            'diferenciais' => 'Carne artesanal',
            'processo_producao' => 'Lanches montados na hora',
            'publico_alvo' => 'Familias do bairro',
            'tom_de_voz' => 'Descolado e divertido',
            'politicas' => 'Troca em 7 dias',
            'cases_exemplo' => 'Cliente: tem X? Bot: temos sim',
        ]);

        $this->assertDatabaseHas('whatsapp_bot_training', [
            'cliente_id' => $cliente->id,
            'historia_negocio' => 'Comecamos em 2015 na garagem',
            'diferenciais' => 'Carne artesanal',
            'processo_producao' => 'Lanches montados na hora',
            'tom_de_voz' => 'Descolado e divertido',
        ]);
    }

    public function test_customer_context_primeira_interacao_sem_cliente_final()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999990000',
            'contact_name' => 'Fulano',
            'contact_phone' => '5511999990000',
            'is_bot_active' => true,
            'last_message_at' => now(),
        ]);

        $ctx = app(WhatsappBotService::class)->buildCustomerContext($chat);

        $this->assertTrue($ctx['is_first_interaction']);
        $this->assertFalse($ctx['is_identified_customer']);
        $this->assertFalse($ctx['is_returning_customer']);
        $this->assertNull($ctx['last_order']);
        $this->assertEquals('Fulano', $ctx['customer_name']);
    }

    public function test_customer_context_cliente_identificado_com_pedido_concluido()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $cf = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'Casa da Maria',
            'nome_responsavel' => 'Maria Silva',
            'telefone' => '5511999991111',
        ]);

        PedidoCliente::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'titulo' => 'Pedido de aniversario',
            'valor' => 150.00,
            'status' => 'Concluído',
            'data_pedido' => now()->subDays(10),
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'wa_id' => '5511999991111',
            'contact_phone' => '5511999991111',
            'is_bot_active' => true,
            'last_message_at' => now(),
        ]);

        \Illuminate\Support\Facades\Cache::flush();
        $ctx = app(WhatsappBotService::class)->buildCustomerContext($chat);

        $this->assertTrue($ctx['is_identified_customer']);
        $this->assertTrue($ctx['is_returning_customer']);
        $this->assertEquals(1, $ctx['total_pedidos_fechados']);
        $this->assertNotNull($ctx['last_order']);
        $this->assertEquals('Pedido de aniversario', $ctx['last_order']['titulo']);
        $this->assertEquals('Concluído', $ctx['last_order']['status']);
        $this->assertEquals('Maria Silva', $ctx['customer_name']);
    }

    public function test_customer_context_aniversario_hoje_sinalizado()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $cf = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'Pessoa Fisica',
            'nome_responsavel' => 'Joao',
            'telefone' => '5511999992222',
            'data_aniversario_responsavel' => now()->year(1990),
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'wa_id' => '5511999992222',
            'contact_phone' => '5511999992222',
            'is_bot_active' => true,
            'last_message_at' => now(),
        ]);

        \Illuminate\Support\Facades\Cache::flush();
        $ctx = app(WhatsappBotService::class)->buildCustomerContext($chat);

        $this->assertTrue($ctx['aniversario_hoje']);
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
