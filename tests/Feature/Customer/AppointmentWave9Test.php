<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Models\Appointment;
use App\Models\Cliente;
use App\Models\User;
use App\Models\WhatsappBotInteraction;
use App\Models\WhatsappChat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentWave9Test extends TestCase
{
    use RefreshDatabase;

    protected function makeCliente(): Cliente
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create(['razao_social' => 'Wave9', 'user_id' => $user->id]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());
        return $cliente;
    }

    public function test_vista_mensal_renderiza_vazia()
    {
        $this->makeCliente();
        $response = $this->get(route('customer.agenda.index', ['view' => 'month']));
        $response->assertOk();
        $response->assertSee('Mês anterior');
        $response->assertSee('Este mês');
    }

    public function test_vista_mensal_lista_appointments_do_mes()
    {
        $cliente = $this->makeCliente();
        $meio = now()->startOfMonth()->addDays(10)->setTime(14, 0);
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Reuniao do mes',
            'scheduled_at' => $meio,
            'duration_minutes' => 60,
            'status' => 'agendado',
        ]);

        $response = $this->get(route('customer.agenda.index', ['view' => 'month']));
        $response->assertOk();
        $response->assertSee('Reuniao do mes');
    }

    public function test_dashboard_calcula_taxa_de_conversao()
    {
        $cliente = $this->makeCliente();

        $chatA = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999991111',
            'contact_phone' => '5511999991111',
            'last_message_at' => now(),
        ]);
        $chatB = WhatsappChat::create([
            'cliente_id' => $cliente->id,
            'wa_id' => '5511999992222',
            'contact_phone' => '5511999992222',
            'last_message_at' => now(),
        ]);

        WhatsappBotInteraction::create(['cliente_id' => $cliente->id, 'chat_id' => $chatA->id]);
        WhatsappBotInteraction::create(['cliente_id' => $cliente->id, 'chat_id' => $chatB->id]);

        // Só A virou appointment
        Appointment::create([
            'cliente_id' => $cliente->id,
            'chat_id' => $chatA->id,
            'titulo' => 'Via bot A',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => 'agendado',
            'created_via' => 'bot',
        ]);

        $response = $this->get(route('customer.whatsapp.dashboard'));
        $response->assertOk();
        $response->assertSee('Taxa de conversão');
        $response->assertSee('50,0%'); // 1 de 2
    }

    public function test_dashboard_calcula_no_show_rate()
    {
        $cliente = $this->makeCliente();

        // Passado concluído
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Compareceu',
            'scheduled_at' => now()->subDays(5),
            'duration_minutes' => 30,
            'status' => 'concluido',
        ]);
        // Passado agendado (no-show)
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Faltou',
            'scheduled_at' => now()->subDays(3),
            'duration_minutes' => 30,
            'status' => 'agendado',
        ]);
        // Passado confirmado (no-show tambem)
        Appointment::create([
            'cliente_id' => $cliente->id,
            'titulo' => 'Faltou tbm',
            'scheduled_at' => now()->subDays(2),
            'duration_minutes' => 30,
            'status' => 'confirmado',
        ]);

        $response = $this->get(route('customer.whatsapp.dashboard'));
        $response->assertOk();
        $response->assertSee('Taxa de no-show');
        $response->assertSee('66,7%'); // 2 de 3 ativos nao concluidos
    }

    public function test_dashboard_mostra_horario_mais_marcado()
    {
        $cliente = $this->makeCliente();
        // 3 agendamentos as 14h, 1 as 10h
        foreach ([14, 14, 14, 10] as $i => $h) {
            Appointment::create([
                'cliente_id' => $cliente->id,
                'titulo' => "Agd $i",
                'scheduled_at' => now()->addDays($i + 1)->setTime($h, 0),
                'duration_minutes' => 30,
                'status' => 'agendado',
            ]);
        }

        $response = $this->get(route('customer.whatsapp.dashboard'));
        $response->assertOk();
        $response->assertSee('Horário mais marcado');
        $response->assertSee('14h');
    }

    public function test_admin_financeiro_renderiza_e_agrega_por_cliente()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $uA = User::factory()->create(['role' => 'cliente']);
        $uB = User::factory()->create(['role' => 'cliente']);
        $clienteA = Cliente::create(['razao_social' => 'Alpha Pizzaria', 'user_id' => $uA->id]);
        $clienteB = Cliente::create(['razao_social' => 'Beta Oficina', 'user_id' => $uB->id]);

        $chatA = WhatsappChat::create([
            'cliente_id' => $clienteA->id, 'wa_id' => '55A', 'last_message_at' => now(),
        ]);
        $chatB = WhatsappChat::create([
            'cliente_id' => $clienteB->id, 'wa_id' => '55B', 'last_message_at' => now(),
        ]);

        WhatsappBotInteraction::create([
            'cliente_id' => $clienteA->id, 'chat_id' => $chatA->id,
            'tokens_in' => 100, 'tokens_out' => 50, 'cost' => 0.005,
        ]);
        WhatsappBotInteraction::create([
            'cliente_id' => $clienteA->id, 'chat_id' => $chatA->id,
            'tokens_in' => 100, 'tokens_out' => 50, 'cost' => 0.005,
        ]);
        WhatsappBotInteraction::create([
            'cliente_id' => $clienteB->id, 'chat_id' => $chatB->id,
            'tokens_in' => 50, 'tokens_out' => 25, 'cost' => 0.001,
        ]);

        $response = $this->get(route('admin.whatsapp-financeiro.index'));
        $response->assertOk();
        $response->assertSee('Alpha Pizzaria');
        $response->assertSee('Beta Oficina');
        // Alpha deve aparecer antes (cost desc)
        $response->assertSeeInOrder(['Alpha Pizzaria', 'Beta Oficina']);
    }

    public function test_admin_financeiro_exige_role_admin()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create(['razao_social' => 'X', 'user_id' => $user->id]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());

        $response = $this->get(route('admin.whatsapp-financeiro.index'));
        $response->assertStatus(403);
    }

    public function test_admin_financeiro_toggle_mes_atual()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.whatsapp-financeiro.index', ['periodo' => 'mes_atual']));
        $response->assertOk();
        $response->assertSee('Mês atual');
    }
}
