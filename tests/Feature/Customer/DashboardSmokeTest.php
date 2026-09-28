<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_do_cliente_renderiza_sem_500()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create([
            'id' => 1,
            'razao_social' => 'Empresa Teste',
            'user_id' => $user->id,
        ]);
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());

        $response = $this->get(route('customer.index'));
        $response->assertOk();
        $response->assertSee('Empresa Teste');
        // Blocos temáticos novos
        $response->assertSee('Da NC5 pra você');
        $response->assertSee('Seu negócio na plataforma');
    }
}
