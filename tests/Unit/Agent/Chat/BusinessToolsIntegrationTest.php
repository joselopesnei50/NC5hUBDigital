<?php

declare(strict_types=1);

namespace Tests\Unit\Agent\Chat;

use App\Agent\Chat\ToolRegistry;
use App\Agent\Chat\Tools\GetDocumentsOverviewTool;
use App\Agent\Chat\Tools\GetOrdersOverviewTool;
use App\Agent\Chat\Tools\GetProductsCatalogTool;
use App\Agent\Chat\Tools\GetSuppliersOverviewTool;
use App\Agent\Metrics\OrdersMetric;
use App\Models\Cliente;
use App\Models\ClienteFinal;
use App\Models\Documento;
use App\Models\Fornecedor;
use App\Models\PedidoCliente;
use App\Models\PedidoClienteItem;
use App\Models\ProdutoServico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessToolsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function seedCliente(int $id = 1, string $nome = 'Tenant Teste'): Cliente
    {
        $user = User::factory()->create();
        return Cliente::create(['id' => $id, 'razao_social' => $nome, 'user_id' => $user->id]);
    }

    public function test_new_business_tools_are_registered_in_tool_registry(): void
    {
        /** @var ToolRegistry $registry */
        $registry = $this->app->make(ToolRegistry::class);

        $this->assertInstanceOf(GetOrdersOverviewTool::class, $registry->getTool('get_orders_overview'));
        $this->assertInstanceOf(GetProductsCatalogTool::class, $registry->getTool('get_products_catalog'));
        $this->assertInstanceOf(GetSuppliersOverviewTool::class, $registry->getTool('get_suppliers_overview'));
        $this->assertInstanceOf(GetDocumentsOverviewTool::class, $registry->getTool('get_documents_overview'));

        $tools = $registry->getToolsForLlm();
        $names = array_map(fn ($t) => $t['function']['name'], $tools);

        $this->assertContains('get_orders_overview', $names);
        $this->assertContains('get_products_catalog', $names);
        $this->assertContains('get_suppliers_overview', $names);
        $this->assertContains('get_documents_overview', $names);
    }

    public function test_get_orders_overview_tool_and_orders_metric_isolate_tenant(): void
    {
        $this->seedCliente(1, 'Tenant 1');
        $this->seedCliente(2, 'Tenant 2');

        $cf1 = ClienteFinal::create(['cliente_id' => 1, 'nome' => 'Maria Silva', 'telefone' => '11999999999']);
        $cf2 = ClienteFinal::create(['cliente_id' => 2, 'nome' => 'Joao Souza', 'telefone' => '11888888888']);

        $p1 = PedidoCliente::create([
            'cliente_id' => 1,
            'cliente_final_id' => $cf1->id,
            'titulo' => 'Combo X-Bacon Artesanal',
            'valor' => 45.00,
            'status' => 'Em Produção',
            'data_pedido' => now(),
        ]);

        PedidoClienteItem::create([
            'pedido_cliente_id' => $p1->id,
            'nome_item' => 'Hambúrguer Artesanal Bacon',
            'quantidade' => 1,
            'valor_unitario' => 35.00,
            'valor_total' => 35.00,
        ]);

        PedidoClienteItem::create([
            'pedido_cliente_id' => $p1->id,
            'nome_item' => 'Batata Frita',
            'quantidade' => 1,
            'valor_unitario' => 10.00,
            'valor_total' => 10.00,
        ]);

        // Pedido de outro tenant
        PedidoCliente::create([
            'cliente_id' => 2,
            'cliente_final_id' => $cf2->id,
            'titulo' => 'Pedido Tenant 2',
            'valor' => 999.00,
            'status' => 'Concluído',
            'data_pedido' => now(),
        ]);

        $ordersTool = new GetOrdersOverviewTool();
        $res = $ordersTool->execute(1, ['period_days' => 30]);

        $this->assertSame(1, $res['resumo']['total_pedidos']);
        $this->assertSame(45.0, (float) $res['resumo']['faturamento_total']);
        $this->assertSame(45.0, (float) $res['resumo']['ticket_medio']);
        $this->assertCount(1, $res['pedidos_recentes']);
        $this->assertSame('Maria Silva', $res['pedidos_recentes'][0]['cliente']);
        $this->assertCount(2, $res['pedidos_recentes'][0]['itens']);

        // Testar OrdersMetric também
        $ordersMetric = new OrdersMetric();
        $metricRes = $ordersMetric->calculate(1, 30);
        $this->assertSame(1, $metricRes['total_pedidos']);
        $this->assertSame(1, $metricRes['pedidos_em_aberto']);
        $this->assertSame(45.0, (float) $metricRes['faturamento_periodo']);
    }

    public function test_get_products_catalog_tool_returns_menu_and_isolates_tenant(): void
    {
        $this->seedCliente(1, 'Tenant 1');
        $this->seedCliente(2, 'Tenant 2');

        ProdutoServico::create([
            'cliente_id' => 1,
            'tipo' => 'produto',
            'nome' => 'Smash Burger Duplo',
            'descricao' => '2 carnes, queijo cheddar e bacon',
            'preco_padrao' => 32.50,
            'unidade_medida' => 'un',
        ]);

        ProdutoServico::create([
            'cliente_id' => 2,
            'tipo' => 'produto',
            'nome' => 'Imóvel Apartamento Luxo',
            'preco_padrao' => 850000.00,
        ]);

        $productsTool = new GetProductsCatalogTool();
        $res = $productsTool->execute(1, ['busca' => 'Smash']);

        $this->assertSame(1, $res['total_encontrados']);
        $this->assertSame('Smash Burger Duplo', $res['itens'][0]['nome']);
        $this->assertSame(32.50, (float) $res['itens'][0]['preco']);
    }

    public function test_get_suppliers_overview_tool_returns_suppliers_and_isolates_tenant(): void
    {
        $this->seedCliente(1, 'Tenant 1');
        $this->seedCliente(2, 'Tenant 2');

        Fornecedor::create([
            'cliente_id' => 1,
            'nome' => 'Distribuidora Carnes Nobres',
            'categoria' => 'Alimentos',
            'telefone' => '11977776666',
            'email' => 'vendas@carnesnobres.com',
        ]);

        Fornecedor::create([
            'cliente_id' => 2,
            'nome' => 'Fornecedor de Outro Cliente',
            'categoria' => 'Outros',
        ]);

        $suppliersTool = new GetSuppliersOverviewTool();
        $res = $suppliersTool->execute(1, []);

        $this->assertSame(1, $res['total_encontrados']);
        $this->assertSame('Distribuidora Carnes Nobres', $res['fornecedores'][0]['nome']);
        $this->assertSame('Alimentos', $res['fornecedores'][0]['categoria']);
    }

    public function test_get_documents_overview_tool_returns_documents_and_isolates_tenant(): void
    {
        $this->seedCliente(1, 'Tenant 1');
        $this->seedCliente(2, 'Tenant 2');

        Documento::create([
            'cliente_id' => 1,
            'nome' => 'Alvará de Funcionamento 2026.pdf',
            'arquivo_path' => 'documentos/alvara.pdf',
            'tipo' => 'outro',
            'enviado_por' => 'cliente',
        ]);

        Documento::create([
            'cliente_id' => 2,
            'nome' => 'Contrato Secreto Tenant 2.pdf',
            'arquivo_path' => 'documentos/contrato2.pdf',
            'tipo' => 'contrato',
            'enviado_por' => 'admin',
        ]);

        $docsTool = new GetDocumentsOverviewTool();
        $res = $docsTool->execute(1, []);

        $this->assertSame(1, $res['total_documentos']);
        $this->assertSame('Alvará de Funcionamento 2026.pdf', $res['documentos'][0]['nome']);
    }
}
