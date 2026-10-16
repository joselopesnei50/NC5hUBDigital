<?php

declare(strict_types=1);

namespace Tests\Unit\Customer;

use App\Models\Cliente;
use App\Models\ClienteFinal;
use App\Models\LandingPage;
use App\Models\ProdutoServico;
use App\Models\User;
use App\Services\GoogleBusinessProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageAndGmbUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedCliente(): array
    {
        $user = User::factory()->create([
            'role' => 'cliente',
        ]);

        $cliente = Cliente::create([
            'user_id' => $user->id,
            'razao_social' => 'Clínica Vida Plena Ltda',
            'telefone' => '(11) 98888-7777',
        ]);

        $user->update(['cliente_id' => $cliente->id]);

        return [$user, $cliente];
    }

    public function test_customer_can_open_editor_and_update_landing_page_across_four_models(): void
    {
        [$user, $cliente] = $this->createAuthenticatedCliente();

        // 1. Acessar o editor provisiona automaticamente a LP inicial
        $resp = $this->actingAs($user)->get(route('customer.landing-page.edit'));
        $resp->assertStatus(200);

        $lp = LandingPage::where('cliente_id', $cliente->id)->first();
        $this->assertNotNull($lp);
        $this->assertEquals('corporativo', $lp->template);
        $this->assertFalse($lp->is_published);

        // 2. Testar atualização trocando para cada um dos 4 modelos e publicando
        foreach (array_keys(LandingPage::TEMPLATES) as $tplKey) {
            $updateResp = $this->actingAs($user)->put(route('customer.landing-page.update'), [
                'template' => $tplKey,
                'slug' => 'clinica-vida-plena',
                'is_published' => 1,
                'titulo_pagina' => 'Clínica Vida Plena',
                'meta_descricao' => 'Especialistas em saúde e estética.',
                'cor_primaria' => '#10b981',
                'cor_secundaria' => '#0f172a',
                'hero_badge' => 'Atendimento Premium',
                'hero_titulo' => 'Sua saúde em primeiro lugar',
                'hero_subtitulo' => 'Equipe multidisciplinar pronta para cuidar de você.',
                'hero_cta_texto' => 'Agendar Consulta',
                'sobre_titulo' => 'Nossa História',
                'sobre_texto' => 'Mais de 10 anos de tradição.',
                'servicos' => [
                    ['titulo' => 'Avaliação Completa', 'descricao' => 'Checkup detalhado', 'preco' => 'R$ 200,00', 'destaque' => 'Popular'],
                ],
                'diferenciais' => [
                    ['titulo' => 'Tecnologia de Ponta', 'descricao' => 'Equipamentos modernos'],
                ],
                'depoimentos' => [
                    ['nome' => 'Maria Silva', 'cargo' => 'Paciente', 'texto' => 'Atendimento nota 10!', 'estrelas' => 5],
                ],
                'faq' => [
                    ['pergunta' => 'Aceita convênio?', 'resposta' => 'Trabalhamos com reembolso e particular.'],
                ],
                'whatsapp_numero' => '11988887777',
                'telefone' => '(11) 3333-2222',
                'email_contato' => 'contato@vidaplena.com.br',
                'endereco_texto' => 'Av. Brasil, 500 - São Paulo/SP',
                'horario_atendimento' => 'Seg a Sex, 08h às 19h',
            ]);

            $updateResp->assertRedirect(route('customer.landing-page.edit'));
            $lp->refresh();
            $this->assertEquals($tplKey, $lp->template);
            $this->assertTrue($lp->is_published);

            // Renderizar a página pública com este template
            $publicResp = $this->get(route('public.landing-page.show', ['slug' => 'clinica-vida-plena']));
            $publicResp->assertStatus(200);
            $publicResp->assertSee('Sua saúde em primeiro lugar');
            $publicResp->assertSee('Avaliação Completa');
        }
    }

    public function test_public_landing_page_captures_lead_into_customer_crm_and_tracks_whatsapp(): void
    {
        [$user, $cliente] = $this->createAuthenticatedCliente();

        $lp = LandingPage::create([
            'cliente_id' => $cliente->id,
            'slug' => 'solar-energia',
            'template' => 'captura_alta_conversao',
            'is_published' => true,
            'titulo_pagina' => 'Solar Energia',
            'cor_primaria' => '#f59e0b',
            'cor_secundaria' => '#0f172a',
            'hero_titulo' => 'Economize até 95% na conta de luz',
            'hero_cta_texto' => 'Simular Grátis',
            'whatsapp_numero' => '11977776666',
        ]);

        // Enviar lead como visitante anônimo
        $leadResp = $this->post(route('public.landing-page.lead', ['slug' => 'solar-energia']), [
            'nome' => 'Roberto Almeida',
            'telefone' => '(11) 91234-5678',
            'email' => 'roberto@exemplo.com',
            'cidade' => 'Campinas',
            'mensagem' => 'Conta média de R$ 800 por mês',
        ]);

        $leadResp->assertRedirect();
        $lp->refresh();
        $this->assertEquals(1, $lp->leads_count);

        $crmLead = ClienteFinal::where('cliente_id', $cliente->id)
            ->where('nome_responsavel', 'Roberto Almeida')
            ->first();

        $this->assertNotNull($crmLead);
        $this->assertEquals('(11) 91234-5678', $crmLead->telefone);
        $this->assertEquals('Campinas', $crmLead->cidade);
        $this->assertStringContainsString('Conta média de R$ 800 por mês', (string) $crmLead->ultimos_pedidos);

        // Testar redirecionamento de WhatsApp e incremento do contador
        $waResp = $this->get(route('public.landing-page.whatsapp', ['slug' => 'solar-energia', 'msg' => 'Quero orçamento']));
        $waResp->assertRedirect();
        $this->assertStringContainsString('wa.me/5511977776666', (string) $waResp->headers->get('Location'));
        $lp->refresh();
        $this->assertEquals(1, $lp->whatsapp_clicks_count);
    }

    public function test_customer_can_import_catalog_items_into_landing_page(): void
    {
        [$user, $cliente] = $this->createAuthenticatedCliente();

        ProdutoServico::create([
            'cliente_id' => $cliente->id,
            'tipo' => 'servico',
            'nome' => 'Clareamento Dental',
            'descricao' => 'Sessão completa a laser',
            'preco_padrao' => 450.00,
            'unidade_medida' => 'sessão',
        ]);

        $resp = $this->actingAs($user)->post(route('customer.landing-page.import-catalog'));
        $resp->assertRedirect(route('customer.landing-page.edit'));

        $lp = LandingPage::where('cliente_id', $cliente->id)->first();
        $this->assertNotNull($lp);
        $this->assertCount(1, $lp->servicos_json);
        $this->assertEquals('Clareamento Dental', $lp->servicos_json[0]['titulo']);
        $this->assertEquals('R$ 450,00', $lp->servicos_json[0]['preco']);
    }

    public function test_google_business_profile_service_update_location_builds_patch_payload_and_mask(): void
    {
        [$user, $cliente] = $this->createAuthenticatedCliente();

        $capturedCall = [];
        $service = new class extends GoogleBusinessProfileService {
            public array $captured = [];
            public function __construct() {}
            protected function setClientForCliente($cliente): void {}
            protected function request($cliente, string $method, string $url, array $options, string $contexto): array
            {
                $this->captured = compact('method', 'url', 'options', 'contexto');
                return ['name' => 'locations/998877', 'title' => 'Clínica Vida Plena Atualizada'];
            }
        };

        $result = $service->updateLocation($cliente, 'accounts/111/locations/998877', [
            'title' => 'Clínica Vida Plena Atualizada',
            'description' => 'Atendimento humanizado e especializado em São Paulo.',
            'primary_phone' => '+55 11 98888-7777',
            'website_uri' => 'https://nc5.com.br/lp/clinica-vida-plena',
            'address' => [
                'address_line' => 'Av. Paulista, 1578',
                'sublocality' => 'Bela Vista',
                'locality' => 'São Paulo',
                'administrative_area' => 'sp',
                'postal_code' => '01310-200',
            ],
            'hours' => [
                'MONDAY' => ['enabled' => 1, 'open' => '08:30', 'close' => '18:00'],
                'SUNDAY' => ['enabled' => 0, 'open' => '08:00', 'close' => '12:00'],
            ],
        ]);

        $this->assertEquals('PATCH', $service->captured['method']);
        $this->assertStringContainsString('locations/998877?updateMask=', $service->captured['url']);

        $json = $service->captured['options']['json'];
        $this->assertEquals('Clínica Vida Plena Atualizada', $json['title']);
        $this->assertEquals('Atendimento humanizado e especializado em São Paulo.', $json['profile']['description']);
        $this->assertEquals('+55 11 98888-7777', $json['phoneNumbers']['primaryPhone']);
        $this->assertEquals('https://nc5.com.br/lp/clinica-vida-plena', $json['websiteUri']);
        $this->assertEquals('SP', $json['storefrontAddress']['administrativeArea']);
        $this->assertEquals(['Av. Paulista, 1578', 'Bela Vista'], $json['storefrontAddress']['addressLines']);
        $this->assertCount(1, $json['regularHours']['periods']);
        $this->assertEquals('MONDAY', $json['regularHours']['periods'][0]['openDay']);
        $this->assertEquals(8, $json['regularHours']['periods'][0]['openTime']['hours']);
        $this->assertEquals(30, $json['regularHours']['periods'][0]['openTime']['minutes']);
    }

    public function test_super_admin_hub_google_business_and_whatsapp_stay_in_admin_panel_and_hide_system_cliente(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Acessar Google Meu Negócio pelo painel Admin mantém o usuário como Admin (sem impersonate)
        $gmbResp = $this->actingAs($admin)->get(route('admin.hub.google-business.index'));
        $gmbResp->assertStatus(200);
        $gmbResp->assertSee('Super Admin');
        $gmbResp->assertSee(route('admin.hub.google-business.connect'), false);
        $gmbResp->assertDontSee('Modo "Entrar como"');
        $this->assertEquals($admin->id, auth()->id());
        $this->assertEquals('admin', auth()->user()->role);

        // 2. Acessar todas as telas do Hub NC5 (Agenda, WhatsApp Conexão, Inbox, Bot IA, Dashboard) mantém o layout Admin unificado
        foreach ([
            'admin.hub.agenda.index',
            'admin.hub.whatsapp.conexao',
            'admin.hub.whatsapp.inbox',
            'admin.hub.whatsapp.bot.index',
            'admin.hub.whatsapp.dashboard',
        ] as $routeName) {
            $hubResp = $this->actingAs($admin)->get(route($routeName));
            $hubResp->assertStatus(200);
            $hubResp->assertSee('Super Admin');
            $hubResp->assertSee(route('admin.hub.google-business.index'), false);
            $hubResp->assertDontSee(route('admin.impersonate.enter'), false);
            $this->assertEquals($admin->id, auth()->id());
        }

        // 3. O registro interno "NC5 Interno" (is_system = true) não aparece na listagem comercial de clientes nem no dashboard
        $clientesResp = $this->actingAs($admin)->get(route('admin.clientes.index'));
        $clientesResp->assertStatus(200);
        $clientesResp->assertDontSee('NC5 Interno');

        $dashResp = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResp->assertStatus(200);
        $dashResp->assertDontSee('NC5 Interno');
    }

    public function test_customer_dashboard_renders_both_nc5_communications_and_business_command_center(): void
    {
        [$user, $cliente] = $this->createAuthenticatedCliente();

        \App\Models\Material::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Campanha Outubro Criativos',
            'status_aprovacao' => 'pendente',
        ]);

        \App\Models\Fatura::create([
            'cliente_id' => $cliente->id,
            'descricao' => 'Mensalidade Agência NC5',
            'valor' => 1500.00,
            'vencimento' => now()->addDays(5),
            'status' => 'pendente',
        ]);

        \App\Models\Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Avaliação Presencial',
            'scheduled_at' => now()->addHours(2),
            'duration_minutes' => 60,
            'status' => 'confirmado',
            'created_via' => 'bot',
        ]);

        $resp = $this->actingAs($user)->get(route('customer.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Comando do Seu Negócio');
        $resp->assertSee('Canais de Atração & Atendimento', false);
        $resp->assertSee('Central NC5');
        $resp->assertSee('Campanha Outubro Criativos');
        $resp->assertSee('Mensalidade Agência NC5');
        $resp->assertSee('Avaliação Presencial');
    }
}
