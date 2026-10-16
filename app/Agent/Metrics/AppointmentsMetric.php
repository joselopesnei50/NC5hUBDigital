<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentsMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'agenda';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $now = Carbon::now();
        $startDate = $now->copy()->subDays(max(1, $periodDays))->startOfDay();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $next7DaysEnd = $now->copy()->addDays(7)->endOfDay();
        $overdueThreshold = $now->copy()->subHours(2);

        $agendamentosHoje = Appointment::where('cliente_id', $tenantId)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->whereBetween('scheduled_at', [$todayStart, $todayEnd])
            ->count();

        $proximos7Dias = Appointment::where('cliente_id', $tenantId)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->whereBetween('scheduled_at', [$now, $next7DaysEnd])
            ->count();

        $vencidosSemBaixa = Appointment::where('cliente_id', $tenantId)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->where('scheduled_at', '<', $overdueThreshold)
            ->count();

        $periodStats = DB::table('appointments')
            ->where('cliente_id', $tenantId)
            ->whereBetween('scheduled_at', [$startDate, $todayEnd])
            ->select(
                DB::raw('COUNT(*) as total_periodo'),
                DB::raw("SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as concluidos"),
                DB::raw("SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados"),
                DB::raw("SUM(CASE WHEN created_via = 'bot' THEN 1 ELSE 0 END) as via_bot")
            )
            ->first();

        $totalPeriodo = (int) ($periodStats->total_periodo ?? 0);
        $concluidos = (int) ($periodStats->concluidos ?? 0);
        $cancelados = (int) ($periodStats->cancelados ?? 0);
        $viaBot = (int) ($periodStats->via_bot ?? 0);

        $taxaCancelamento = $totalPeriodo > 0
            ? round(($cancelados / $totalPeriodo) * 100, 1)
            : 0.0;

        return [
            'agendamentos_hoje' => $agendamentosHoje,
            'proximos_7_dias' => $proximos7Dias,
            'total_periodo' => $totalPeriodo,
            'concluidos_periodo' => $concluidos,
            'cancelados_periodo' => $cancelados,
            'taxa_cancelamento_pct' => $taxaCancelamento,
            'vencidos_sem_baixa' => $vencidosSemBaixa,
            'criados_via_bot_periodo' => $viaBot,
        ];
    }
}
