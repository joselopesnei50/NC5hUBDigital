<?php

declare(strict_types=1);

namespace Tests\Unit\Agent;

use Tests\TestCase;
use App\Agent\SnapshotBuilder;
use App\Agent\Metrics\CashFlowMetric;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class AgentMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_flow_metric_aggregates_correctly_and_isolates_tenant()
    {
        // Fixture: Tenant 1 (O que está sendo analisado)
        DB::table('clientes')->insert(['id' => 1, 'razao_social' => 'Tenant 1']);
        
        DB::table('lancamentos_financeiros')->insert([
            ['cliente_id' => 1, 'tipo' => 'receber', 'valor' => 1500.00, 'status' => 'pago', 'descricao' => 'A', 'data_vencimento' => now()],
            ['cliente_id' => 1, 'tipo' => 'pagar', 'valor' => 500.00, 'status' => 'pago', 'descricao' => 'B', 'data_vencimento' => now()],
        ]);

        // Fixture: Tenant 2 (Dados que NÃO devem vazar para o Tenant 1)
        DB::table('clientes')->insert(['id' => 2, 'razao_social' => 'Tenant 2']);
        
        DB::table('lancamentos_financeiros')->insert([
            ['cliente_id' => 2, 'tipo' => 'receber', 'valor' => 10000.00, 'status' => 'pago', 'descricao' => 'Vazamento', 'data_vencimento' => now()],
        ]);

        $metric = new CashFlowMetric();
        $result = $metric->calculate(tenantId: 1, periodDays: 30);

        // Asserts
        $this->assertEquals(1500.00, $result['receitas_realizadas_brl']);
        $this->assertEquals(500.00, $result['despesas_realizadas_brl']);
        $this->assertEquals(1000.00, $result['saldo_operacional_brl']); // 1500 - 500
    }

    public function test_snapshot_builder_compiles_all_metrics()
    {
        $mockMetric = $this->createMock(\App\Agent\Contracts\MetricInterface::class);
        $mockMetric->method('getName')->willReturn('mock_metric');
        $mockMetric->method('calculate')->willReturn(['mocked_key' => 123]);

        $builder = new SnapshotBuilder([$mockMetric]);
        $snapshot = $builder->build(tenantId: 1, periodDays: 30);

        $this->assertArrayHasKey('metricas', $snapshot);
        $this->assertArrayHasKey('mock_metric', $snapshot['metricas']);
        $this->assertEquals(123, $snapshot['metricas']['mock_metric']['mocked_key']);
        $this->assertEquals(1, $snapshot['tenant_id']);
    }
}
