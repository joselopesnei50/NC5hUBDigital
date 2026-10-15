<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Agent\Chat\Tools\CriarAgendamentoWhatsappTool;
use App\Models\Appointment;
use App\Models\AppointmentResource;
use App\Models\Cliente;
use App\Models\ClienteFinal;
use App\Models\User;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Services\AppointmentScheduler;
use App\Services\CalendarTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentWave8Test extends TestCase
{
    use RefreshDatabase;

    protected function makeCliente(): Cliente
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create(['razao_social' => 'Wave8', 'user_id' => $user->id]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());
        return $cliente;
    }

    protected function trainingSegSex(int $clienteId): WhatsappBotTraining
    {
        $horario = [];
        foreach (['seg', 'ter', 'qua', 'qui', 'sex'] as $d) {
            $horario[$d] = ['aberto' => true, 'abre' => '09:00', 'fecha' => '18:00'];
        }
        $horario['sab'] = ['aberto' => false, 'abre' => '09:00', 'fecha' => '13:00'];
        $horario['dom'] = ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'];
        return WhatsappBotTraining::create([
            'cliente_id' => $clienteId,
            'horario_comercial' => $horario,
        ]);
    }

    protected function nextDayOfWeekAt(int $dow, int $hour, int $min = 0): Carbon
    {
        $when = now()->copy()->setTime($hour, $min);
        while ($when->isPast() || $when->dayOfWeek !== $dow) {
            $when->addDay()->setTime($hour, $min);
        }
        return $when;
    }

    public function test_scheduler_usa_horario_do_recurso_quando_custom()
    {
        $cliente = $this->makeCliente();
        $training = $this->trainingSegSex($cliente->id);

        // Recurso que trabalha SÓ terça e quinta
        $recurso = AppointmentResource::create([
            'cliente_id' => $cliente->id,
            'nome' => 'Dra. Ana',
            'is_active' => true,
            'use_custom_hours' => true,
            'horario_comercial' => [
                'seg' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
                'ter' => ['aberto' => true, 'abre' => '14:00', 'fecha' => '20:00'],
                'qua' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
                'qui' => ['aberto' => true, 'abre' => '14:00', 'fecha' => '20:00'],
                'sex' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
                'sab' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '13:00'],
                'dom' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
            ],
        ]);

        $sched = app(AppointmentScheduler::class);

        $terca15 = $this->nextDayOfWeekAt(2, 15);
        $this->assertTrue($sched->isWithinBusinessHours($terca15, 60, $training, $recurso));

        $terca10 = $this->nextDayOfWeekAt(2, 10);
        $this->assertFalse($sched->isWithinBusinessHours($terca10, 60, $training, $recurso),
            'Terça 10h devia ser rejeitado — recurso só abre às 14h');

        $segunda15 = $this->nextDayOfWeekAt(1, 15);
        $this->assertFalse($sched->isWithinBusinessHours($segunda15, 60, $training, $recurso),
            'Segunda devia ser rejeitado — recurso fechado nesse dia');
    }

    public function test_scheduler_fallback_training_quando_recurso_sem_custom()
    {
        $cliente = $this->makeCliente();
        $training = $this->trainingSegSex($cliente->id);

        $recurso = AppointmentResource::create([
            'cliente_id' => $cliente->id,
            'nome' => 'Normal',
            'is_active' => true,
            'use_custom_hours' => false,
        ]);

        $sched = app(AppointmentScheduler::class);
        $segunda10 = $this->nextDayOfWeekAt(1, 10);

        $this->assertTrue($sched->isWithinBusinessHours($segunda10, 60, $training, $recurso),
            'Sem custom hours, deve usar o training (segunda 10h ok)');
    }

    public function test_tool_criar_rejeita_horario_fora_do_recurso_custom()
    {
        $cliente = $this->makeCliente();
        $this->trainingSegSex($cliente->id);

        $recurso = AppointmentResource::create([
            'cliente_id' => $cliente->id,
            'nome' => 'So terca',
            'is_active' => true,
            'use_custom_hours' => true,
            'horario_comercial' => [
                'seg' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
                'ter' => ['aberto' => true, 'abre' => '09:00', 'fecha' => '18:00'],
                'qua' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
                'qui' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
                'sex' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
                'sab' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '13:00'],
                'dom' => ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'],
            ],
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999991111',
            'contact_phone' => '5511999991111',
            'last_message_at' => now(),
        ]);

        $segunda = $this->nextDayOfWeekAt(1, 14);
        $result = (new CriarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'data_hora' => $segunda->format('Y-m-d H:i'),
            'titulo' => 'Vai falhar',
            'resource_id' => $recurso->id,
        ]);

        $this->assertFalse($result['ok']);
        $this->assertEquals('horario_fora_do_expediente', $result['erro']);
    }

    public function test_ics_cliente_retorna_appointments()
    {
        $cliente = $this->makeCliente();
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Reunião ABC',
            'descricao' => 'detalhes',
            'contato_nome' => 'Joao',
            'scheduled_at' => now()->addDays(2)->setTime(14, 0),
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Antigo',
            'scheduled_at' => now()->subMonths(5),
            'duration_minutes' => 30,
            'status' => 'concluido',
        ]);

        $token = app(CalendarTokenService::class)->ensureToken($cliente);
        $this->assertNotEmpty($token);

        $response = $this->get(route('public.calendar.cliente', ['token' => $token]));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $response->assertSee('BEGIN:VCALENDAR');
        $response->assertSee('SUMMARY:Reunião ABC');
        $response->assertDontSee('SUMMARY:Antigo');
    }

    public function test_ics_cliente_token_invalido_404()
    {
        $this->get(route('public.calendar.cliente', ['token' => str_repeat('x', 48)]))->assertNotFound();
    }

    public function test_ics_cliente_token_curto_404()
    {
        $this->get(route('public.calendar.cliente', ['token' => 'abc']))->assertNotFound();
    }

    public function test_ics_cliente_final_filtra_so_appointments_dele()
    {
        $cliente = $this->makeCliente();

        $cf1 = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'E1', 'nome_responsavel' => 'Fulano',
            'telefone' => '5511999991000',
        ]);
        $cf2 = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'E2', 'nome_responsavel' => 'Beltrano',
            'telefone' => '5511999992000',
        ]);

        Appointment::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf1->id,
            'titulo' => 'Meu agendamento',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf2->id,
            'titulo' => 'Agendamento do beltrano',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);

        $token = app(CalendarTokenService::class)->ensureToken($cf1);
        $response = $this->get(route('public.calendar.cliente-final', ['token' => $token]));
        $response->assertOk();
        $response->assertSee('SUMMARY:Meu agendamento');
        $response->assertDontSee('SUMMARY:Agendamento do beltrano');
    }

    public function test_regenerar_token_cliente_troca_valor()
    {
        $cliente = $this->makeCliente();
        $old = app(CalendarTokenService::class)->ensureToken($cliente);

        $this->post(route('customer.calendar.cliente.regenerate'))->assertRedirect();

        $new = $cliente->fresh()->calendar_token;
        $this->assertNotEmpty($new);
        $this->assertNotEquals($old, $new);

        $this->get(route('public.calendar.cliente', ['token' => $old]))->assertNotFound();
        $this->get(route('public.calendar.cliente', ['token' => $new]))->assertOk();
    }

    public function test_regenerar_token_cliente_final_isola_cross_tenant()
    {
        $this->makeCliente();

        $outroUser = User::factory()->create(['role' => 'cliente']);
        $outroCliente = Cliente::create(['razao_social' => 'Outro', 'user_id' => $outroUser->id]);
        $outroCf = ClienteFinal::create([
            'cliente_id' => $outroCliente->id,
            'nome_empresa' => 'X', 'nome_responsavel' => 'X',
            'telefone' => '5511999993000',
        ]);

        $response = $this->post(route('customer.calendar.cliente-final.regenerate', $outroCf));
        $response->assertNotFound();
    }

    public function test_ics_contem_location_do_recurso()
    {
        $cliente = $this->makeCliente();
        $recurso = AppointmentResource::create([
            'cliente_id' => $cliente->id,
            'nome' => 'Dr. Costa',
            'is_active' => true,
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'resource_id' => $recurso->id,
            'titulo' => 'Consulta',
            'scheduled_at' => now()->addDay()->setTime(14, 0),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);

        $token = app(CalendarTokenService::class)->ensureToken($cliente);
        $response = $this->get(route('public.calendar.cliente', ['token' => $token]));
        $response->assertOk();
        $response->assertSee('LOCATION:Dr. Costa');
    }
}
