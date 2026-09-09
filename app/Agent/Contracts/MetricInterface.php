<?php

declare(strict_types=1);

namespace App\Agent\Contracts;

interface MetricInterface
{
    /**
     * Retorna a chave que representará esta métrica no Snapshot JSON.
     */
    public function getName(): string;

    /**
     * Calcula e agrega a métrica isolando por tenant.
     * O retorno é sempre um array estruturado (DTO em formato array) pronto para JSON.
     */
    public function calculate(int $tenantId, int $periodDays): array;
}
