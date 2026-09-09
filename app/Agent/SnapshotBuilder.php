<?php

declare(strict_types=1);

namespace App\Agent;

use App\Agent\Contracts\MetricInterface;

class SnapshotBuilder
{
    /**
     * @param MetricInterface[] $metrics Coleção de métricas a serem processadas
     */
    public function __construct(
        private array $metrics
    ) {
    }

    /**
     * Constrói o estado atual (Snapshot) do negócio de um tenant para enviar ao LLM.
     */
    public function build(int $tenantId, int $periodDays = 30): array
    {
        $snapshot = [
            'tenant_id' => $tenantId,
            'analise_gerada_em' => now()->toIso8601String(),
            'periodo_dias' => $periodDays,
            'metricas' => [],
        ];

        foreach ($this->metrics as $metric) {
            $snapshot['metricas'][$metric->getName()] = $metric->calculate($tenantId, $periodDays);
        }

        return $snapshot;
    }
}
