<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashFlowMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'fluxo_caixa';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $startDate = Carbon::now()->subDays($periodDays)->startOfDay();

        // Agregação direta no banco garantindo a inegociável restrição do tenant_id
        $metrics = DB::table('lancamentos_financeiros')
            ->where('cliente_id', $tenantId)
            ->where('data_vencimento', '>=', $startDate)
            ->select(
                DB::raw('SUM(CASE WHEN tipo = "receber" AND status = "pago" THEN valor ELSE 0 END) as receitas_realizadas'),
                DB::raw('SUM(CASE WHEN tipo = "pagar" AND status = "pago" THEN valor ELSE 0 END) as despesas_realizadas'),
                DB::raw('SUM(CASE WHEN tipo = "receber" AND status = "pendente" AND data_vencimento < CURRENT_DATE() THEN valor ELSE 0 END) as inadimplencia_estimada')
            )
            ->first();

        $receitas = (float) ($metrics->receitas_realizadas ?? 0);
        $despesas = (float) ($metrics->despesas_realizadas ?? 0);

        return [
            'receitas_realizadas_brl' => $receitas,
            'despesas_realizadas_brl' => $despesas,
            'inadimplencia_estimada_brl' => (float) ($metrics->inadimplencia_estimada ?? 0),
            'saldo_operacional_brl' => $receitas - $despesas,
        ];
    }
}
