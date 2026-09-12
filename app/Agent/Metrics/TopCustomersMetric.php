<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TopCustomersMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'top_clientes';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $startDate = Carbon::now()->subDays($periodDays)->startOfDay();

        $rows = DB::table('pedidos_clientes as p')
            ->join('clientes_finais as c', 'c.id', '=', 'p.cliente_final_id')
            ->join('pedido_cliente_itens as i', 'i.pedido_cliente_id', '=', 'p.id')
            ->where('p.cliente_id', $tenantId)
            ->where('p.created_at', '>=', $startDate)
            ->whereNotIn('p.status', ['Cancelado'])
            ->groupBy('c.id', 'c.nome_empresa')
            ->select(
                'c.id as cliente_final_id',
                'c.nome_empresa',
                DB::raw('COUNT(DISTINCT p.id) as qtd_pedidos'),
                DB::raw('SUM(i.valor_total) as total_gasto'),
                DB::raw('MAX(p.created_at) as ultima_compra')
            )
            ->orderByDesc('total_gasto')
            ->limit(5)
            ->get();

        $top = $rows->map(function ($row) {
            return [
                'cliente_final_id' => (int) $row->cliente_final_id,
                'nome' => $row->nome_empresa,
                'qtd_pedidos' => (int) $row->qtd_pedidos,
                'total_gasto_brl' => (float) $row->total_gasto,
                'ultima_compra' => $row->ultima_compra,
            ];
        })->all();

        return [
            'top_clientes_no_periodo' => $top,
            'quantidade_analisada' => count($top),
        ];
    }
}
