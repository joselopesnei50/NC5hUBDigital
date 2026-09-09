<?php

declare(strict_types=1);

namespace Tests\Unit\Agent\Chat;

use Tests\TestCase;
use App\Agent\Chat\ToolRegistry;
use App\Agent\Chat\Tools\GetCashFlowTool;
use App\Agent\Metrics\CashFlowMetric;

class ToolRegistryTest extends TestCase
{
    public function test_registry_formats_tools_properly_for_llm_schema()
    {
        $metricMock = $this->createMock(CashFlowMetric::class);
        $tool = new GetCashFlowTool($metricMock);

        $registry = new ToolRegistry();
        $registry->register($tool);

        $schemaArray = $registry->getToolsForLlm();

        $this->assertCount(1, $schemaArray);
        $this->assertEquals('function', $schemaArray[0]['type']);
        $this->assertEquals('get_cash_flow', $schemaArray[0]['function']['name']);
        $this->assertArrayHasKey('properties', $schemaArray[0]['function']['parameters']);
    }

    public function test_tools_pass_tenant_id_correctly()
    {
        $metricMock = $this->createMock(CashFlowMetric::class);
        // Garante que o isolamento tenant_id = 99 chegue até o banco, 
        // bloqueando que a IA manipule parâmetros do sistema e ataque outro tenant
        $metricMock->expects($this->once())
                   ->method('calculate')
                   ->with($this->equalTo(99), $this->equalTo(60))
                   ->willReturn(['sucesso' => true]);

        $tool = new GetCashFlowTool($metricMock);
        
        // A IA tenta acessar, mas nós do Backend forçamos o ID 99 de forma inegociável
        $result = $tool->execute(99, ['period_days' => 60]);

        $this->assertTrue($result['sucesso']);
    }
}
