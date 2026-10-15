<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Agent\Chat\Tools\CriarAgendamentoWhatsappTool;
use App\Agent\Chat\Tools\ListarRecursosWhatsappTool;
use App\Models\Appointment;
use App\Models\AppointmentResource;
use App\Models\Cliente;
use App\Models\User;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Services\AppointmentScheduler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function makeCliente(): Cliente
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create(['razao_social' => 'Wave7', 'user_id' => $user->id]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());
        return $cliente;
    }

    protected function nextWeekdayAt(int $hour): Carbon
    {
        $when = now()->copy()->setTime($hour, 0);
        while ($when->isPast() || $when->dayOfWeek === 0 || $when->dayOfWeek === 6) {
            $when->addDay()->setTime($hour, 0);
        }
        return $when;
    }

    public function test_crud_recurso_cria_edita_exclui()
    {
        $cliente = $this->makeCliente();

        $this->get(route('customer.agenda.recursos.index'))->assertOk()->assertSee('Nenhum');

        $this->post(route('customer.agenda.recursos.store'), [
            'nome' => 'Dr. Silva',
            'descricao' => 'Cardio',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('appointment_resources', [
            'cliente_id' => $cliente->id,
            'nome' => 'Dr. Silva',
            'is_active' => 1,
        ]);

        $r = AppointmentResource::where('cliente_id', $cliente->id)->first();

        $this->put(route('customer.agenda.recursos.update', $r), [
            'nome' => 'Dr. Silva Jr',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertEquals('Dr. Silva Jr', $r->fresh()->nome);

        $this->delete(route('customer.agenda.recursos.destroy', $r))->assertRedirect();
        $this->assertDatabaseMissing('appointment_resources', ['id' => $r->id]);
    }

    public function test_recurso_isola_por_cliente()
    {
        $this->makeCliente();
        $outroUser = User::factory()->create(['role' => 'cliente']);
        $outroCliente = Cliente::create(['razao_social' => 'Outro', 'user_id' => $outroUser->id]);
        $outro = AppointmentResource::create([
            'cliente_id' => $outroCliente->id,
            'nome' => 'Nao posso ver',
            'is_active' => true,
        ]);

        $this->get(route('customer.agenda.recursos.edit', $outro))->assertNotFound();
    }

    public function test_conflito_e_por_recurso()
    {
        $cliente = $this->makeCliente();
        $cadeira1 = AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'C1', 'is_active' => true]);
        $cadeira2 = AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'C2', 'is_active' => true]);

        $when = $this->nextWeekdayAt(14);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'resource_id' => $cadeira1->id,
            'titulo' => 'Occupa C1',
            'scheduled_at' => $when,
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);

        $sched = app(AppointmentScheduler::class);
        $this->assertNotNull($sched->findConflict($cliente->id, $when, 60, null, $cadeira1->id));
        $this->assertNull($sched->findConflict($cliente->id, $when, 60, null, $cadeira2->id));
    }

    public function test_tool_auto_preenche_resource_quando_tem_um_ativo()
    {
        $cliente = $this->makeCliente();
        $unico = AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'Unico', 'is_active' => true]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999991111',
            'contact_phone' => '5511999991111',
            'last_message_at' => now(),
        ]);

        $when = $this->nextWeekdayAt(14);
        $result = (new CriarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'data_hora' => $when->format('Y-m-d H:i'),
            'titulo' => 'Corte',
        ]);

        $this->assertTrue($result['ok']);
        $this->assertDatabaseHas('appointments', [
            'cliente_id' => $cliente->id,
            'resource_id' => $unico->id,
            'titulo' => 'Corte',
        ]);
    }

    public function test_tool_exige_escolha_quando_dois_recursos_ativos()
    {
        $cliente = $this->makeCliente();
        AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'C1', 'is_active' => true]);
        AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'C2', 'is_active' => true]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999992222',
            'contact_phone' => '5511999992222',
            'last_message_at' => now(),
        ]);

        $when = $this->nextWeekdayAt(14);
        $result = (new CriarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'data_hora' => $when->format('Y-m-d H:i'),
            'titulo' => 'Tentando sem resource',
        ]);

        $this->assertFalse($result['ok']);
        $this->assertEquals('escolha_recurso_necessaria', $result['erro']);
        $this->assertCount(2, $result['recursos']);
    }

    public function test_tool_resource_zero_aloca_primeiro_disponivel()
    {
        $cliente = $this->makeCliente();
        $c1 = AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'C1', 'ordem' => 1, 'is_active' => true]);
        $c2 = AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'C2', 'ordem' => 2, 'is_active' => true]);

        $when = $this->nextWeekdayAt(14);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'resource_id' => $c1->id,
            'titulo' => 'C1 ocupada',
            'scheduled_at' => $when,
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999993333',
            'contact_phone' => '5511999993333',
            'last_message_at' => now(),
        ]);

        $result = (new CriarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'data_hora' => $when->format('Y-m-d H:i'),
            'titulo' => 'Qualquer um',
            'resource_id' => 0,
        ]);

        $this->assertTrue($result['ok']);
        $this->assertDatabaseHas('appointments', [
            'titulo' => 'Qualquer um',
            'resource_id' => $c2->id,
        ]);
    }

    public function test_tool_rejeita_resource_id_invalido()
    {
        $cliente = $this->makeCliente();
        AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'Valido', 'is_active' => true]);

        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999994444',
            'contact_phone' => '5511999994444',
            'last_message_at' => now(),
        ]);

        $when = $this->nextWeekdayAt(14);
        $result = (new CriarAgendamentoWhatsappTool())->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'data_hora' => $when->format('Y-m-d H:i'),
            'titulo' => 'Test',
            'resource_id' => 999999,
        ]);

        $this->assertFalse($result['ok']);
        $this->assertEquals('recurso_invalido', $result['erro']);
    }

    public function test_tool_listar_recursos_retorna_ativos()
    {
        $cliente = $this->makeCliente();
        AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'Ativo', 'is_active' => true, 'ordem' => 1]);
        AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'Inativo', 'is_active' => false, 'ordem' => 2]);

        $result = (new ListarRecursosWhatsappTool())->execute($cliente->id, []);

        $this->assertEquals(1, $result['total']);
        $this->assertEquals('Ativo', $result['recursos'][0]['nome']);
    }

    public function test_bot_form_persiste_resource_label()
    {
        $cliente = $this->makeCliente();

        $this->post(route('customer.whatsapp.bot.update'), [
            'segment' => 'salao_beleza',
            'resource_label' => 'Cadeira',
        ]);

        $this->assertDatabaseHas('whatsapp_bot_training', [
            'cliente_id' => $cliente->id,
            'resource_label' => 'Cadeira',
        ]);
    }

    public function test_crud_manual_cria_appointment_com_resource()
    {
        $cliente = $this->makeCliente();
        $recurso = AppointmentResource::create(['cliente_id' => $cliente->id, 'nome' => 'Dr X', 'is_active' => true]);

        $this->post(route('customer.agenda.store'), [
            'titulo' => 'Consulta',
            'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
            'scheduled_time' => '14:00',
            'duration_minutes' => 30,
            'resource_id' => $recurso->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'cliente_id' => $cliente->id,
            'resource_id' => $recurso->id,
            'titulo' => 'Consulta',
        ]);
    }
}
