<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Cliente;
use App\Models\User;
use App\Services\SystemCliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonateTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_normal_nao_pode_acessar_enter()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $this->actingAs($user);

        $response = $this->get(route('admin.impersonate.enter'));
        $response->assertStatus(403);
    }

    public function test_admin_entra_e_vira_cliente_sistema()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.impersonate.enter', ['target' => 'whatsapp.bot']));
        $response->assertRedirect(route('customer.whatsapp.bot.index'));

        $this->assertDatabaseHas('clientes', [
            'is_system' => 1,
            'razao_social' => 'NC5 Interno',
        ]);

        $cliente = SystemCliente::get();
        $user = User::where('id', auth()->id())->first();
        $this->assertNotNull($user);
        $this->assertEquals('cliente', $user->role);
        $this->assertEquals($cliente->id, $user->cliente_id);

        $this->assertEquals($admin->id, session('impersonating_admin_user_id'));
    }

    public function test_leave_volta_ao_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('admin.impersonate.enter'));

        $this->assertNotEquals($admin->id, auth()->id());
        $this->assertEquals($admin->id, session('impersonating_admin_user_id'));

        $response = $this->post(route('admin.impersonate.leave'));
        $response->assertRedirect(route('admin.dashboard'));

        $this->assertEquals($admin->id, auth()->id());
        $this->assertNull(session('impersonating_admin_user_id'));
    }

    public function test_enter_redireciona_pro_target_correto()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $cases = [
            'whatsapp.inbox' => 'customer.whatsapp.inbox',
            'whatsapp.bot' => 'customer.whatsapp.bot.index',
            'whatsapp.dashboard' => 'customer.whatsapp.dashboard',
            'google-business' => 'customer.google-business.index',
        ];

        foreach ($cases as $target => $expectedRoute) {
            $this->refreshApplication();
            $admin2 = User::factory()->create(['role' => 'admin']);
            $this->actingAs($admin2);
            $response = $this->get(route('admin.impersonate.enter', ['target' => $target]));
            $response->assertRedirect(route($expectedRoute));
        }
    }

    public function test_default_target_vai_para_conexao()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.impersonate.enter'));
        $response->assertRedirect(route('customer.whatsapp.conexao'));
    }
}
