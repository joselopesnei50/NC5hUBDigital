<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrdersMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'pedidos_e_vendas';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $startDate = Carbon::now()->subDays($periodDays)->startOfDay();

        $query = DB::table('pedidos_clientes')
            ->where('cliente_id', $tenantId)
            ->where('created_at', '>=', $startDate);

        $totalPedidos = (clone $query)->count();
        $valorTotal = (float) (clone $query)->whereNotIn('status', ['Cancelado'])->sum('valor');
        $ticketMedio = $totalPedidos > 0 ? round($valorTotal / $totalPedidos, 2) : 0.0;

        $porStatus = (clone $query)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Pedidos pendentes ou em produção precisando de atenção
        $emAberto = (clone $query)
            ->whereIn('status', ['Pendente', 'Em Produção', 'Em Andamento', 'Aprovado'])
            ->count();

        return [
            'total_pedidos' => $totalPedidos,
            'pedidos_em_aberto' => $emAberto,
            'faturamento_periodo' => $valorTotal,
            'ticket_medio' => $ticketMedio,
            'por_status' => $porStatus,
            'dias_analisados' => $periodDays,
        ];
    }
}
