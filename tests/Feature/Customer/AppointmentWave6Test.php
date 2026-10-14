<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Models\Appointment;
use App\Models\Cliente;
use App\Models\User;
use App\Models\WhatsappBotInteraction;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use App\Services\EvolutionApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentWave6Test extends TestCase
{
    use RefreshDatabase;

    protected function makeCliente(): Cliente
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create(['razao_social' => 'Wave6', 'user_id' => $user->id]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());
        return $cliente;
    }

    public function test_vista_semana_renderiza_vazia()
    {
        $this->makeCliente();
        $response = $this->get(route('customer.agenda.index', ['view' => 'week']));
        $response->assertOk();
        $response->assertSee('Semana');
        $response->assertSee('Semana anterior');
        $response->assertSee('Esta semana');
    }

    public function test_vista_semana_lista_appointments_da_semana()
    {
        $cliente = $this->makeCliente();
        $meio = now()->startOfWeek(\Carbon\Carbon::MONDAY)->addDays(2)->setTime(14, 30);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Reuniao da semana',
            'scheduled_at' => $meio,
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);

        $response = $this->get(route('customer.agenda.index', ['view' => 'week']));
        $response->assertOk();
        $response->assertSee('Reuniao da semana');
    }

    public function test_bot_form_persiste_segundo_lembrete()
    {
        $cliente = $this->makeCliente();

        $this->post(route('customer.whatsapp.bot.update'), [
            'segment' => 'lanchonete',
            'enable_second_reminder' => '1',
            'reminder_hours_before_second' => '48',
        ]);

        $this->assertDatabaseHas('whatsapp_bot_training', [
            'cliente_id' => $cliente->id,
            'enable_second_reminder' => 1,
            'reminder_hours_before_second' => 48,
        ]);
    }

    public function test_segundo_lembrete_envia_sem_impactar_primeiro()
    {
        $cliente = $this->makeCliente();
        WhatsappBotTraining::create([
            'cliente_id' => $cliente->id,
            'business_name' => 'Test',
            'reminder_hours_before' => 2,
            'enable_second_reminder' => true,
            'reminder_hours_before_second' => 24,
        ]);
        WhatsappInstance::create([
            'cliente_id' => $cliente->id,
            'instance_name' => 'wave6',
            'instance_token' => str_repeat('c', 64),
            'status' => 'open',
        ]);
        $apt = Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Agenda 2 lembretes',
            'contato_telefone' => '5511999997700',
            'scheduled_at' => now()->addHours(24)->addMinutes(5),
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);

        $evo = \Mockery::mock(EvolutionApiService::class);
        $evo->shouldReceive('isConfigured')->andReturn(true);
        $evo->shouldReceive('sendText')->once()->andReturn(['key' => ['id' => 'wa_1']]);
        $this->app->instance(EvolutionApiService::class, $evo);

        $this->artisan('appointments:send-reminders')->assertExitCode(0);

        $apt->refresh();
        $this->assertNotNull($apt->reminder_second_sent_at);
        $this->assertNull($apt->reminder_sent_at);
    }

    public function test_segundo_lembrete_nao_reenvia()
    {
        $cliente = $this->makeCliente();
        WhatsappBotTraining::create([
            'cliente_id' => $cliente->id,
            'reminder_hours_before' => 2,
            'enable_second_reminder' => true,
            'reminder_hours_before_second' => 24,
        ]);
        WhatsappInstance::create([
            'cliente_id' => $cliente->id,
            'instance_name' => 'wave6-dup',
            'instance_token' => str_repeat('d', 64),
            'status' => 'open',
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Ja enviei o segundo',
            'contato_telefone' => '5511999997711',
            'scheduled_at' => now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'agendado',
            'reminder_second_sent_at' => now()->subMinute(),
        ]);

        $evo = \Mockery::mock(EvolutionApiService::class);
        $evo->shouldReceive('isConfigured')->andReturn(true);
        $evo->shouldNotReceive('sendText');
        $this->app->instance(EvolutionApiService::class, $evo);

        $this->artisan('appointments:send-reminders')->assertExitCode(0);
    }

    public function test_cancelled_at_marcado_via_controller()
    {
        $cliente = $this->makeCliente();
        $apt = Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Vai cancelar',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);

        $this->post(route('customer.agenda.cancel', $apt))->assertRedirect();

        $apt->refresh();
        $this->assertEquals('cancelado', $apt->status);
        $this->assertNotNull($apt->cancelled_at);
    }

    public function test_dashboard_mostra_metricas_de_agenda()
    {
        $cliente = $this->makeCliente();
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Manual',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
            'created_via' => 'manual',
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Via bot',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
            'created_via' => 'bot',
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Cancelado',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'cancelado',
            'cancelled_at' => now(),
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999998800',
            'contact_phone' => '5511999998800',
            'last_message_at' => now(),
        ]);
        WhatsappBotInteraction::create([
            'cliente_id' => $cliente->id,
            'chat_id' => $chat->id,
            'tool_errors' => [['tool' => 'criar_agendamento', 'error' => 'conflito_de_horario']],
        ]);
        WhatsappBotInteraction::create([
            'cliente_id' => $cliente->id,
            'chat_id' => $chat->id,
            'tool_errors' => [['tool' => 'criar_agendamento', 'error' => 'horario_fora_do_expediente']],
        ]);

        $response = $this->get(route('customer.whatsapp.dashboard'));
        $response->assertOk();
        $response->assertSee('Agenda do período');
        $response->assertSee('Agendamentos criados');
        $response->assertSee('Conflitos de horário');
        $response->assertSee('Fora do expediente');
    }
}
