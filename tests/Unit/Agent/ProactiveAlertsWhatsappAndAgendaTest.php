<?php

declare(strict_types=1);

namespace Tests\Unit\Agent;

use App\Agent\Chat\ToolRegistry;
use App\Agent\Chat\Tools\GetAgendaOverviewTool;
use App\Agent\Chat\Tools\GetWhatsappOverviewTool;
use App\Agent\Metrics\AppointmentsMetric;
use App\Agent\Metrics\WhatsappAtendimentoMetric;
use App\Agent\Proactive\ProactiveAlertsService;
use App\Agent\SnapshotBuilder;
use App\Models\AgentAlert;
use App\Models\Appointment;
use App\Models\Cliente;
use App\Models\User;
use App\Models\WhatsappBotInteraction;
use App\Models\WhatsappBotProfile;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProactiveAlertsWhatsappAndAgendaTest extends TestCase
{
    use RefreshDatabase;

    private function seedCliente(int $id = 1, string $nome = 'Tenant Teste'): Cliente
    {
        $user = User::factory()->create();
        return Cliente::create(['id' => $id, 'razao_social' => $nome, 'user_id' => $user->id]);
    }

    private function serviceWithMetrics(array $metricas): ProactiveAlertsService
    {
        $snapshot = [
            'tenant_id' => 1,
            'analise_gerada_em' => now()->toIso8601String(),
            'periodo_dias' => 30,
            'metricas' => $metricas,
        ];

        $mock = $this->createMock(SnapshotBuilder::class);
        $mock->method('build')->willReturn($snapshot);

        return new ProactiveAlertsService($mock);
    }

    public function test_appointments_metric_calculates_and_isolates_tenant(): void
    {
        $this->seedCliente(1, 'Tenant 1');
        $this->seedCliente(2, 'Tenant 2');

        // Hoje ativo (Tenant 1)
        Appointment::create([
            'cliente_id' => 1,
            'titulo' => 'Consulta Hoje',
            'scheduled_at' => now()->addHours(1),
            'duration_minutes' => 60,
            'status' => 'confirmado',
            'created_via' => 'bot',
        ]);

        // Vencido ontem sem baixa (Tenant 1)
        Appointment::create([
            'cliente_id' => 1,
            'titulo' => 'Consulta Passada Sem Baixa',
            'scheduled_at' => now()->subDay(),
            'duration_minutes' => 60,
            'status' => 'agendado',
            'created_via' => 'manual',
        ]);

        // Cancelado há 2 dias (Tenant 1)
        Appointment::create([
            'cliente_id' => 1,
            'titulo' => 'Consulta Cancelada',
            'scheduled_at' => now()->subDays(2),
            'duration_minutes' => 60,
            'status' => 'cancelado',
            'created_via' => 'bot',
        ]);

        // Concluído há 3 dias (Tenant 1)
        Appointment::create([
            'cliente_id' => 1,
            'titulo' => 'Consulta Concluída',
            'scheduled_at' => now()->subDays(3),
            'duration_minutes' => 60,
            'status' => 'concluido',
            'created_via' => 'manual',
        ]);

        // Outro tenant (não deve vazar)
        Appointment::create([
            'cliente_id' => 2,
            'titulo' => 'Vazamento Tenant 2',
            'scheduled_at' => now()->addHours(2),
            'duration_minutes' => 60,
            'status' => 'agendado',
            'created_via' => 'bot',
        ]);

        $metric = new AppointmentsMetric();
        $res = $metric->calculate(1, 30);

        $this->assertSame(1, $res['agendamentos_hoje']);
        $this->assertSame(4, $res['total_periodo']);
        $this->assertSame(1, $res['concluidos_periodo']);
        $this->assertSame(1, $res['cancelados_periodo']);
        $this->assertSame(25.0, $res['taxa_cancelamento_pct']);
        $this->assertSame(1, $res['vencidos_sem_baixa']);
        $this->assertSame(2, $res['criados_via_bot_periodo']);
    }

    public function test_whatsapp_atendimento_metric_calculates_and_isolates_tenant(): void
    {
        $this->seedCliente(1, 'Tenant 1');
        $this->seedCliente(2, 'Tenant 2');

        WhatsappInstance::create([
            'cliente_id' => 1,
            'instance_name' => 'nc5_t1',
            'instance_token' => 'tok1',
            'status' => 'open',
        ]);

        WhatsappBotProfile::create([
            'cliente_id' => 1,
            'segment' => 'clinica_saude',
            'is_active' => true,
        ]);

        $chat1 = WhatsappChat::create([
            'cliente_id' => 1,
            'wa_id' => '5511999990001',
            'contact_name' => 'Maria',
            'contact_phone' => '5511999990001',
            'is_group' => false,
            'is_bot_active' => false,
            'unread_count' => 3,
            'last_message_at' => now()->subMinutes(15),
        ]);

        $chat2 = WhatsappChat::create([
            'cliente_id' => 1,
            'wa_id' => '5511999990002',
            'contact_name' => 'João',
            'contact_phone' => '5511999990002',
            'is_group' => false,
            'is_bot_active' => true,
            'unread_count' => 0,
            'last_message_at' => now()->subHours(2),
        ]);

        // Chat de outro tenant
        WhatsappChat::create([
            'cliente_id' => 2,
            'wa_id' => '5511999990003',
            'contact_name' => 'Outro',
            'is_group' => false,
            'is_bot_active' => false,
            'unread_count' => 5,
            'last_message_at' => now(),
        ]);

        WhatsappBotInteraction::create([
            'cliente_id' => 1,
            'chat_id' => $chat1->id,
            'response_time_ms' => 1200,
            'escalated' => true,
            'failed' => false,
        ]);

        WhatsappBotInteraction::create([
            'cliente_id' => 1,
            'chat_id' => $chat2->id,
            'response_time_ms' => 800,
            'escalated' => false,
            'failed' => true,
        ]);

        $metric = new WhatsappAtendimentoMetric();
        $res = $metric->calculate(1, 30);

        $this->assertTrue($res['instancia_configurada']);
        $this->assertTrue($res['instancia_conectada']);
        $this->assertTrue($res['bot_ativo']);
        $this->assertSame(2, $res['conversas_ativas_periodo']);
        $this->assertSame(1, $res['conversas_nao_lidas']);
        $this->assertSame(1, $res['conversas_aguardando_humano']);
        $this->assertSame(2, $res['interacoes_bot_periodo']);
        $this->assertSame(1, $res['escalacoes_periodo']);
        $this->assertSame(1, $res['falhas_bot_periodo']);
        $this->assertSame(1000, $res['tempo_medio_resposta_ms']);
    }

    public function test_proactive_alerts_whatsapp_and_agenda_rules(): void
    {
        $this->seedCliente(1);

        $criados = $this->serviceWithMetrics([
            'whatsapp_atendimento' => [
                'instancia_configurada' => true,
                'instancia_conectada' => false,
                'status_instancia' => 'close',
                'conversas_aguardando_humano' => 2,
                'interacoes_bot_periodo' => 10,
                'falhas_bot_periodo' => 4,
            ],
            'agenda' => [
                'vencidos_sem_baixa' => 4,
                'total_periodo' => 10,
                'cancelados_periodo' => 4,
                'taxa_cancelamento_pct' => 40.0,
            ],
        ])->analyzeTenant(1);

        $this->assertCount(5, $criados);
        $this->assertNotNull(AgentAlert::where('tipo', 'whatsapp_desconectado')->where('severidade', 'critico')->first());
        $this->assertNotNull(AgentAlert::where('tipo', 'whatsapp_aguardando_humano')->where('severidade', 'atencao')->first());
        $this->assertNotNull(AgentAlert::where('tipo', 'whatsapp_falhas_bot')->where('severidade', 'atencao')->first());
        $this->assertNotNull(AgentAlert::where('tipo', 'agenda_vencidos_sem_baixa')->where('severidade', 'info')->first());
        $this->assertNotNull(AgentAlert::where('tipo', 'agenda_cancelamento_alto')->where('severidade', 'atencao')->first());
    }

    public function test_tools_agenda_and_whatsapp_overview_are_registered_and_execute(): void
    {
        $this->seedCliente(1);

        Appointment::create([
            'cliente_id' => 1,
            'titulo' => 'Reunião de Alinhamento',
            'contato_nome' => 'Carlos Silva',
            'scheduled_at' => now()->addHours(3),
            'duration_minutes' => 45,
            'status' => 'confirmado',
            'created_via' => 'bot',
        ]);

        WhatsappChat::create([
            'cliente_id' => 1,
            'wa_id' => '5511988887777',
            'contact_name' => 'Ana Souza',
            'contact_phone' => '5511988887777',
            'is_group' => false,
            'is_bot_active' => false,
            'unread_count' => 2,
            'last_message_at' => now(),
        ]);

        /** @var ToolRegistry $registry */
        $registry = $this->app->make(ToolRegistry::class);

        $agendaTool = $registry->getTool('get_agenda_overview');
        $waTool = $registry->getTool('get_whatsapp_overview');

        $this->assertInstanceOf(GetAgendaOverviewTool::class, $agendaTool);
        $this->assertInstanceOf(GetWhatsappOverviewTool::class, $waTool);

        $agendaOut = $agendaTool->execute(1, []);
        $this->assertSame(1, $agendaOut['total_listados']);
        $this->assertSame('Reunião de Alinhamento', $agendaOut['compromissos'][0]['titulo']);
        $this->assertSame('Carlos Silva', $agendaOut['compromissos'][0]['cliente_nome']);

        $waOut = $waTool->execute(1, ['period_days' => 30]);
        $this->assertSame(1, $waOut['metricas']['conversas_aguardando_humano']);
        $this->assertCount(1, $waOut['conversas_em_atencao']);
        $this->assertSame('aguardando_humano', $waOut['conversas_em_atencao'][0]['status_atendimento']);
    }
}
