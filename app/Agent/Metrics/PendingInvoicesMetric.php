<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PendingInvoicesMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'faturas_com_nc5';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $today = Carbon::today()->toDateString();

        $rows = DB::table('faturas')
            ->where('cliente_id', $tenantId)
            ->where('status', 'pendente')
            ->select(
                DB::raw('COUNT(*) as total_pendentes'),
                DB::raw("SUM(CASE WHEN vencimento < '{$today}' THEN valor ELSE 0 END) as valor_em_atraso"),
                DB::raw("SUM(CASE WHEN vencimento >= '{$today}' THEN valor ELSE 0 END) as valor_a_vencer"),
                DB::raw("SUM(CASE WHEN vencimento < '{$today}' THEN 1 ELSE 0 END) as qtd_em_atraso")
            )
            ->first();

        return [
            'quantidade_pendentes' => (int) ($rows->total_pendentes ?? 0),
            'quantidade_em_atraso' => (int) ($rows->qtd_em_atraso ?? 0),
            'valor_em_atraso_brl' => (float) ($rows->valor_em_atraso ?? 0),
            'valor_a_vencer_brl' => (float) ($rows->valor_a_vencer ?? 0),
        ];
    }
}
