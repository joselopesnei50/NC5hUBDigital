<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectDeliveryMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'entregas_operacionais';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $startDate = Carbon::now()->subDays($periodDays)->startOfDay();

        $stats = DB::table('projetos')
            ->where('cliente_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('COUNT(*) as total_projetos'),
                DB::raw('SUM(CASE WHEN status = "concluido" THEN 1 ELSE 0 END) as concluidos'),
                DB::raw('SUM(CASE WHEN status = "aguardando_cliente" THEN 1 ELSE 0 END) as gargalo_cliente')
            )
            ->first();

        return [
            'projetos_iniciados_periodo' => (int) ($stats->total_projetos ?? 0),
            'projetos_concluidos' => (int) ($stats->concluidos ?? 0),
            'projetos_travados_pelo_cliente' => (int) ($stats->gargalo_cliente ?? 0),
        ];
    }
}
