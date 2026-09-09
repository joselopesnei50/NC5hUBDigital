<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Agent\Metrics\CashFlowMetric;

class GetCashFlowTool implements ToolInterface
{
    public function __construct(
        private CashFlowMetric $metric
    ) {
    }

    public function getName(): string
    {
        return 'get_cash_flow';
    }

    public function getDescription(): string
    {
        return 'Busca no banco de dados o total de receitas, despesas e saldo operacional do negócio para uma janela de dias (padrão 30). Use sempre que o gestor perguntar sobre finanças ou contas a pagar/receber.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'period_days' => [
                    'type' => 'integer', 
                    'description' => 'Número de dias passados para consolidar (ex: 7, 30, 90).'
                ],
            ]
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $days = (int) ($arguments['period_days'] ?? 30);
        
        // Reutilizamos a métrica altamente testada e segura da Etapa 2
        return $this->metric->calculate($tenantId, $days);
    }
}
