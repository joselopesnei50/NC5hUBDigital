<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Agent\SnapshotBuilder;

class GetBusinessSnapshotTool implements ToolInterface
{
    public function __construct(
        private SnapshotBuilder $snapshotBuilder
    ) {
    }

    public function getName(): string
    {
        return 'get_business_snapshot';
    }

    public function getDescription(): string
    {
        return 'Consolida em uma única chamada o retrato completo do negócio do gestor: '
             . 'fluxo de caixa (receitas/despesas/saldo), faturas com a NC5 (pendentes/em atraso), '
             . 'base de clientes (novos e crescimento), top 5 clientes por gasto, '
             . 'clientes sumidos (que já compraram e pararam), performance de produtos '
             . '(top vendidos e estagnados) e entregas operacionais (projetos ativos, concluídos, travados). '
             . 'USE SEMPRE ESTA FERRAMENTA quando o gestor fizer perguntas amplas como '
             . '"como estamos?", "faz uma análise", "o que sugere", "diagnóstico geral", '
             . '"visão do negócio" ou quando você precisar cruzar mais de uma métrica '
             . 'antes de responder. É a forma mais barata de você entender o momento '
             . 'do negócio — não chame get_cash_flow para o mesmo turno depois.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'period_days' => [
                    'type' => 'integer',
                    'description' => 'Janela em dias para as métricas (padrão 30). Use 7 para "esta semana", 90 para "trimestre".',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $days = (int) ($arguments['period_days'] ?? 30);
        if ($days < 1) $days = 30;
        if ($days > 365) $days = 365;

        return $this->snapshotBuilder->build($tenantId, $days);
    }
}
