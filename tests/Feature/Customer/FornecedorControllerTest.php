<?php

declare(strict_types=1);

namespace Tests\Feature\Customer;

use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FornecedorControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsCliente(int $clienteId = 1): array
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create(['id' => $clienteId, 'razao_social' => 'Empresa Teste', 'user_id' => $user->id]);
        // User.cliente() eh belongsTo — precisa ligar via User.cliente_id
        $user->update(['cliente_id' => $cliente->id]);
        $this->actingAs($user->fresh());
        return [$user->fresh(), $cliente];
    }

    public function test_index_lista_apenas_fornecedores_do_cliente_logado()
    {
        [$user, $cliente] = $this->actingAsCliente(1);
        Fornecedor::create(['cliente_id' => $cliente->id, 'nome' => 'AWS']);
        Fornecedor::create(['cliente_id' => $cliente->id, 'nome' => 'Google']);

        // Cliente 2 nao deve vazar
        $outroUser = User::factory()->create(['role' => 'cliente']);
        $outroCliente = Cliente::create(['id' => 2, 'razao_social' => 'Outra', 'user_id' => $outroUser->id]);
        Fornecedor::create(['cliente_id' => $outroCliente->id, 'nome' => 'Vazamento']);

        $response = $this->get(route('customer.fornecedores.index'));
        $response->assertOk();
        $response->assertSee('AWS');
        $response->assertSee('Google');
        $response->assertDontSee('Vazamento');
    }

    public function test_store_cria_com_cliente_id_do_usuario_autenticado()
    {
        [$user, $cliente] = $this->actingAsCliente(1);

        $response = $this->post(route('customer.fornecedores.store'), [
            'nome' => 'Nova Papelaria',
            'cnpj_cpf' => '00.111.222/0001-33',
            'email' => 'contato@papelaria.com',
            'categoria' => 'Material',
        ]);

        $response->assertRedirect(route('customer.fornecedores.index'));
        $this->assertDatabaseHas('fornecedores', [
            'cliente_id' => $cliente->id,
            'nome' => 'Nova Papelaria',
            'categoria' => 'Material',
        ]);
    }

    public function test_store_requer_nome()
    {
        [$user, $cliente] = $this->actingAsCliente(1);

        $response = $this->post(route('customer.fornecedores.store'), [
            'email' => 'x@y.com',
        ]);

        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseCount('fornecedores', 0);
    }

    public function test_edit_de_fornecedor_de_outro_cliente_retorna_403()
    {
        [$user, $cliente] = $this->actingAsCliente(1);
        $outroUser = User::factory()->create(['role' => 'cliente']);
        $outroCliente = Cliente::create(['id' => 2, 'razao_social' => 'Outra', 'user_id' => $outroUser->id]);
        $alheio = Fornecedor::create(['cliente_id' => $outroCliente->id, 'nome' => 'Alheio']);

        $response = $this->get(route('customer.fornecedores.edit', $alheio));
        $response->assertForbidden();
    }

    public function test_destroy_de_fornecedor_de_outro_cliente_retorna_403()
    {
        [$user, $cliente] = $this->actingAsCliente(1);
        $outroUser = User::factory()->create(['role' => 'cliente']);
        $outroCliente = Cliente::create(['id' => 2, 'razao_social' => 'Outra', 'user_id' => $outroUser->id]);
        $alheio = Fornecedor::create(['cliente_id' => $outroCliente->id, 'nome' => 'Alheio']);

        $response = $this->delete(route('customer.fornecedores.destroy', $alheio));
        $response->assertForbidden();
        $this->assertDatabaseHas('fornecedores', ['id' => $alheio->id]);
    }

    public function test_busca_filtra_por_nome_email_ou_categoria()
    {
        [$user, $cliente] = $this->actingAsCliente(1);
        Fornecedor::create(['cliente_id' => $cliente->id, 'nome' => 'Contabilidade Alpha']);
        Fornecedor::create(['cliente_id' => $cliente->id, 'nome' => 'Beta Software', 'categoria' => 'Contabilidade']);
        Fornecedor::create(['cliente_id' => $cliente->id, 'nome' => 'Zeta Ltda',      'categoria' => 'Papelaria']);

        $response = $this->get(route('customer.fornecedores.index', ['search' => 'Contab']));
        $response->assertOk();
        $response->assertSee('Contabilidade Alpha');
        $response->assertSee('Beta Software');
        $response->assertDontSee('Zeta Ltda');
    }
}
