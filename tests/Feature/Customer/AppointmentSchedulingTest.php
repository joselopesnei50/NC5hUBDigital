<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Agent\Chat\Tools\CancelarAgendamentoWhatsappTool;
use App\Agent\Chat\Tools\CriarAgendamentoWhatsappTool;
use App\Agent\Chat\Tools\ListarMeusAgendamentosWhatsappTool;
use App\Models\Appointment;
use App\Models\Cliente;
use App\Models\ClienteFinal;
use App\Models\User;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Services\AppointmentScheduler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentSchedulingTest extends TestCase
{
    use RefreshDatabase;

    protected function makeCliente(): Cliente
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create(['razao_social' => 'Empresa Sched', 'user_id' => $user->id]);
        $user->update(['cliente_id' => $cliente->id]);
        return $cliente;
    }

    protected function makeTrainingWithBusinessHours(int $clienteId, array $horario = null): WhatsappBotTraining
    {
        $default = [];
        foreach (['seg', 'ter', 'qua', 'qui', 'sex'] as $d) {
            $default[$d] = ['aberto' => true, 'abre' => '09:00', 'fecha' => '18:00'];
        }
        $default['sab'] = ['aberto' => true, 'abre' => '09:00', 'fecha' => '13:00'];
        $default['dom'] = ['aberto' => false, 'abre' => '09:00', 'fecha' => '18:00'];

        return WhatsappBotTraining::create([
            'cliente_id' => $clienteId,
            'horario_comercial' => $horario ?: $default,
        ]);
    }

    protected function nextWeekdayAt(int $hour, int $min = 0, int $daysOfWeek = null): Carbon
    {
        $when = now()->copy()->setTime($hour, $min);
        while ($when->isPast() || $when->dayOfWeek === 0 || $when->dayOfWeek === 6) {
            $when->addDay()->setTime($hour, $min);
        }
        return $when;
    }

    public function test_scheduler_valida_horario_dentro_do_expediente()
    {
        $cliente = $this->makeCliente();
        $training = $this->makeTrainingWithBusinessHours($cliente->id);
        $sched = app(AppointmentScheduler::class);

        $dentro = $this->nextWeekdayAt(14);
        $this->assertTrue($sched->isWithinBusinessHours($dentro, 60, $training));
    }

    public function test_scheduler_rejeita_horario_fora_do_expediente()
    {
        $cliente = $this->makeCliente();
        $training = $this->makeTrainingWithBusinessHours($cliente->id);
        $sched = app(AppointmentScheduler::class);

        $muitoCedo = $this->nextWeekdayAt(7);
        $this->assertFalse($sched->isWithinBusinessHours($muitoCedo, 60, $training));
    }

    public function test_scheduler_rejeita_dia_fechado()
    {
        $cliente = $this->makeCliente();
        $training = $this->makeTrainingWithBusinessHours($cliente->id);
        $sched = app(AppointmentScheduler::class);

        $domingo = now()->next(0)->setTime(14, 0);
        $this->assertFalse($sched->isWithinBusinessHours($domingo, 60, $training));
    }

    public function test_scheduler_detecta_conflito()
    {
        $cliente = $this->makeCliente();
        $training = $this->makeTrainingWithBusinessHours($cliente->id);
        $sched = app(AppointmentScheduler::class);
        $when = $this->nextWeekdayAt(14);

        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Existente',
            'scheduled_at' => $when,
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);

        $conflict = $sched->findConflict($cliente->id, $when->copy()->addMinutes(30), 30);
        $this->assertNotNull($conflict);
    }

    public function test_scheduler_sugere_slots_livres()
    {
        $cliente = $this->makeCliente();
        $training = $this->makeTrainingWithBusinessHours($cliente->id);
        $sched = app(AppointmentScheduler::class);

        $when = $this->nextWeekdayAt(14);
        $slots = $sched->suggestNextSlots($cliente->id, $training, $when, 60, 3);

        $this->assertCount(3, $slots);
        foreach ($slots as $s) {
            $this->assertTrue($sched->isWithinBusinessHours($s, 60, $training));
        }
    }

    public function test_tool_criar_rejeita_fora_do_expediente_com_sugestoes()
    {
        $cliente = $this->makeCliente();
        $this->makeTrainingWithBusinessHours($cliente->id);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999991111',
            'contact_phone' => '5511999991111',
            'last_message_at' => now(),
        ]);

        $when = $this->nextWeekdayAt(7);
        $result = (new CriarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'data_hora' => $when->format('Y-m-d H:i'),
            'titulo' => 'Corte',
        ]);

        $this->assertFalse($result['ok']);
        $this->assertEquals('horario_fora_do_expediente', $result['erro']);
        $this->assertNotEmpty($result['sugestoes']);
    }

    public function test_tool_criar_rejeita_conflito_com_sugestoes()
    {
        $cliente = $this->makeCliente();
        $this->makeTrainingWithBusinessHours($cliente->id);

        $when = $this->nextWeekdayAt(14);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Ja agendado',
            'scheduled_at' => $when,
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999992222',
            'contact_phone' => '5511999992222',
            'last_message_at' => now(),
        ]);

        $result = (new CriarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'data_hora' => $when->format('Y-m-d H:i'),
            'titulo' => 'Tentando',
        ]);

        $this->assertFalse($result['ok']);
        $this->assertEquals('conflito_de_horario', $result['erro']);
        $this->assertNotEmpty($result['sugestoes']);
    }

    public function test_tool_listar_retorna_so_do_cliente_do_chat()
    {
        $cliente = $this->makeCliente();
        $cf = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'X', 'nome_responsavel' => 'Fulano',
            'telefone' => '5511999993333',
        ]);
        $outroCf = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'Y', 'nome_responsavel' => 'Beltrano',
            'telefone' => '5511999994444',
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'wa_id' => '5511999993333',
            'contact_phone' => '5511999993333',
            'last_message_at' => now(),
        ]);

        Appointment::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'titulo' => 'Meu agendamento',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $outroCf->id,
            'titulo' => 'Agenda do beltrano',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);

        $result = (new ListarMeusAgendamentosWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
        ]);

        $this->assertEquals(1, $result['total']);
        $this->assertEquals('Meu agendamento', $result['agendamentos'][0]['titulo']);
    }

    public function test_tool_cancelar_pelo_cliente_dono()
    {
        $cliente = $this->makeCliente();
        $cf = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'X', 'nome_responsavel' => 'Fulano',
            'telefone' => '5511999995555',
        ]);
        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'wa_id' => '5511999995555',
            'contact_phone' => '5511999995555',
            'last_message_at' => now(),
        ]);
        $apt = Appointment::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'titulo' => 'Vai cancelar',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);

        $result = (new CancelarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'appointment_id' => $apt->id,
            'motivo' => 'cliente nao pode',
        ]);

        $this->assertTrue($result['ok']);
        $this->assertEquals('cancelado', $apt->fresh()->status);
        $this->assertStringContainsString('cliente nao pode', $apt->fresh()->notes);
    }

    public function test_tool_cancelar_bloqueia_appointment_de_outro_cliente_final()
    {
        $cliente = $this->makeCliente();
        $cfA = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'A', 'nome_responsavel' => 'A',
            'telefone' => '5511999996666',
        ]);
        $cfB = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'B', 'nome_responsavel' => 'B',
            'telefone' => '5511999997777',
        ]);
        $chatA = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cfA->id,
            'wa_id' => '5511999996666',
            'last_message_at' => now(),
        ]);
        $aptDoB = Appointment::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cfB->id,
            'titulo' => 'Nao e seu',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);

        $result = (new CancelarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chatA->id,
            'appointment_id' => $aptDoB->id,
        ]);

        $this->assertFalse($result['ok']);
        $this->assertEquals('agendado', $aptDoB->fresh()->status);
    }

    public function test_bot_form_salva_horario_comercial()
    {
        $cliente = $this->makeCliente();
        $this->actingAs(User::where('cliente_id', $cliente->id)->first());

        $this->post(route('customer.whatsapp.bot.update'), [
            'segment' => 'lanchonete',
            'horario_comercial' => [
                'seg' => ['aberto' => '1', 'abre' => '08:00', 'fecha' => '20:00'],
                'ter' => ['abre' => '08:00', 'fecha' => '20:00'],
                'dom' => ['aberto' => '1', 'abre' => '10:00', 'fecha' => '14:00'],
            ],
        ]);

        $t = WhatsappBotTraining::where('cliente_id', $cliente->id)->first();
        $this->assertTrue($t->horario_comercial['seg']['aberto']);
        $this->assertEquals('08:00', $t->horario_comercial['seg']['abre']);
        $this->assertFalse($t->horario_comercial['ter']['aberto']);
        $this->assertTrue($t->horario_comercial['dom']['aberto']);
    }
}
