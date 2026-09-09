<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerSegmentMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'segmentacao_clientes';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $startDate = Carbon::now()->subDays($periodDays)->startOfDay();

        $novos = DB::table('clientes_finais')
            ->where('cliente_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->count();

        $totalAtivos = DB::table('clientes_finais')
            ->where('cliente_id', $tenantId)
            ->count();

        return [
            'novos_clientes_periodo' => $novos,
            'total_base_clientes' => $totalAtivos,
            'taxa_crescimento_base_pct' => $totalAtivos > 0 ? round(($novos / $totalAtivos) * 100, 2) : 0,
        ];
    }
}
