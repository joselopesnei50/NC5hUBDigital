<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Agent\Chat\Tools\ConsultarAgendaWhatsappTool;
use App\Agent\Chat\Tools\CriarAgendamentoWhatsappTool;
use App\Console\Commands\SendAppointmentReminders;
use App\Models\Appointment;
use App\Models\Cliente;
use App\Models\ClienteFinal;
use App\Models\User;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use App\Services\EvolutionApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsCliente(): array
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create([
            'razao_social' => 'Empresa Agenda',
            'user_id' => $user->id,
        ]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());

        return [$user->fresh(), $cliente];
    }

    public function test_agenda_lista_vazia_renderiza()
    {
        [$user, $cliente] = $this->actingAsCliente();
        $response = $this->get(route('customer.agenda.index'));
        $response->assertOk();
        $response->assertSee('Nenhum agendamento futuro');
    }

    public function test_criar_agendamento_manual()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $response = $this->post(route('customer.agenda.store'), [
            'titulo' => 'Reuniao com fornecedor',
            'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
            'scheduled_time' => '14:30',
            'duration_minutes' => 60,
            'contato_nome' => 'Joao',
            'contato_telefone' => '5511999991111',
        ]);

        $response->assertRedirect(route('customer.agenda.index'));
        $this->assertDatabaseHas('appointments', [
            'cliente_id' => $cliente->id,
            'titulo' => 'Reuniao com fornecedor',
            'created_via' => 'manual',
            'status' => 'agendado',
        ]);
    }

    public function test_agenda_isola_por_cliente()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $outroUser = User::factory()->create(['role' => 'cliente']);
        $outroCliente = Cliente::create(['razao_social' => 'Outro', 'user_id' => $outroUser->id]);
        $apt = Appointment::create([
            'cliente_id' => $outroCliente->id,
            'titulo' => 'Nao deveria aparecer',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
        ]);

        $this->get(route('customer.agenda.edit', $apt))->assertNotFound();
    }

    public function test_tool_criar_agendamento_funciona()
    {
        [$user, $cliente] = $this->actingAsCliente();

        $cf = ClienteFinal::create([
            'cliente_id' => $cliente->id,
            'nome_empresa' => 'Casa',
            'nome_responsavel' => 'Maria',
            'telefone' => '5511999992222',
        ]);
        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'cliente_final_id' => $cf->id,
            'wa_id' => '5511999992222',
            'contact_name' => 'Maria',
            'contact_phone' => '5511999992222',
            'last_message_at' => now(),
        ]);

        $future = now()->addDays(3)->setTime(15, 0);
        $tool = new CriarAgendamentoWhatsappTool();
        $result = $tool->execute($cliente->id, [
            '_chat_id' => $chat->id,
            '_default_duration' => 45,
            'data_hora' => $future->format('Y-m-d H:i'),
            'titulo' => 'Corte',
        ]);

        $this->assertTrue($result['ok']);
        $this->assertDatabaseHas('appointments', [
            'cliente_id' => $cliente->id,
            'chat_id' => $chat->id,
            'titulo' => 'Corte',
            'duration_minutes' => 45,
            'created_via' => 'bot',
            'contato_telefone' => '5511999992222',
        ]);
    }

    public function test_tool_criar_agendamento_rejeita_data_passada()
    {
        [$user, $cliente] = $this->actingAsCliente();
        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999993333',
            'contact_phone' => '5511999993333',
            'last_message_at' => now(),
        ]);

        $tool = new CriarAgendamentoWhatsappTool();
        $result = $tool->execute($cliente->id, [
            '_chat_id' => $chat->id,
            'data_hora' => now()->subDay()->format('Y-m-d H:i'),
            'titulo' => 'Passado',
        ]);

        $this->assertFalse($result['ok']);
        $this->assertDatabaseMissing('appointments', ['titulo' => 'Passado']);
    }

    public function test_tool_consultar_agenda_retorna_proximos()
    {
        [$user, $cliente] = $this->actingAsCliente();
        $chat = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999994444',
            'contact_phone' => '5511999994444',
            'last_message_at' => now(),
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'chat_id' => $chat->id,
            'titulo' => 'Corte 1',
            'scheduled_at' => now()->addDays(2)->setTime(10, 0),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Fora da janela',
            'scheduled_at' => now()->addDays(15),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);

        $tool = new ConsultarAgendaWhatsappTool();
        $result = $tool->execute($cliente->id, []);

        $this->assertEquals(1, $result['total']);
        $this->assertEquals('Corte 1', $result['agendamentos'][0]['titulo']);
    }

    public function test_reminder_envia_quando_dentro_da_janela()
    {
        [$user, $cliente] = $this->actingAsCliente();

        WhatsappBotTraining::create([
            'cliente_id' => $cliente->id,
            'business_name' => 'Pet Shop Teste',
            'reminder_hours_before' => 2,
        ]);
        WhatsappInstance::create([
            'cliente_id' => $cliente->id,
            'instance_name' => 'teste-inst',
            'instance_token' => str_repeat('a', 64),
            'status' => 'open',
        ]);
        $apt = Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Banho do Rex',
            'contato_nome' => 'Jose',
            'contato_telefone' => '5511999995555',
            'scheduled_at' => now()->addHours(2)->addMinutes(5),
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);

        $evo = \Mockery::mock(EvolutionApiService::class);
        $evo->shouldReceive('isConfigured')->andReturn(true);
        $evo->shouldReceive('sendText')->once()->andReturn(['key' => ['id' => 'wa_x']]);
        $this->app->instance(EvolutionApiService::class, $evo);

        $this->artisan('appointments:send-reminders')->assertExitCode(0);

        $this->assertNotNull($apt->fresh()->reminder_sent_at);
    }

    public function test_reminder_nao_reenvia()
    {
        [$user, $cliente] = $this->actingAsCliente();
        WhatsappInstance::create([
            'cliente_id' => $cliente->id,
            'instance_name' => 'teste-inst-2',
            'instance_token' => str_repeat('b', 64),
            'status' => 'open',
        ]);
        $apt = Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Ja lembrado',
            'contato_telefone' => '5511999996666',
            'scheduled_at' => now()->addHours(2),
            'duration_minutes' => 30,
            'status' => 'agendado',
            'reminder_sent_at' => now()->subMinute(),
        ]);

        $evo = \Mockery::mock(EvolutionApiService::class);
        $evo->shouldReceive('isConfigured')->andReturn(true);
        $evo->shouldNotReceive('sendText');
        $this->app->instance(EvolutionApiService::class, $evo);

        $this->artisan('appointments:send-reminders')->assertExitCode(0);
    }
}
