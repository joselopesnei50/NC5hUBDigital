<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductsPerformanceMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'performance_produtos';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $startDate = Carbon::now()->subDays($periodDays)->startOfDay();

        $vendidos = DB::table('pedido_cliente_itens as i')
            ->join('pedidos_clientes as p', 'p.id', '=', 'i.pedido_cliente_id')
            ->leftJoin('produtos_servicos as ps', 'ps.id', '=', 'i.produto_servico_id')
            ->where('p.cliente_id', $tenantId)
            ->where('p.created_at', '>=', $startDate)
            ->whereNotIn('p.status', ['Cancelado'])
            ->groupBy('i.produto_servico_id')
            ->select(
                'i.produto_servico_id',
                DB::raw('COALESCE(MAX(ps.nome), MAX(i.nome_item)) as nome'),
                DB::raw("COALESCE(MAX(ps.tipo), 'Item avulso') as tipo"),
                DB::raw('SUM(i.quantidade) as qtd_vendida'),
                DB::raw('SUM(i.valor_total) as receita_gerada')
            )
            ->orderByDesc('receita_gerada')
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'produto_servico_id' => $r->produto_servico_id !== null ? (int) $r->produto_servico_id : null,
                'nome' => $r->nome,
                'tipo' => $r->tipo,
                'quantidade_vendida' => (float) $r->qtd_vendida,
                'receita_brl' => (float) $r->receita_gerada,
            ])
            ->all();

        // Produtos cadastrados que NAO venderam nada no periodo (estagnados)
        $vendidosIds = collect($vendidos)->pluck('produto_servico_id')->filter()->all();

        $estagnados = DB::table('produtos_servicos')
            ->where('cliente_id', $tenantId)
            ->when(!empty($vendidosIds), fn ($q) => $q->whereNotIn('id', $vendidosIds))
            ->select('id', 'nome', 'tipo')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'produto_servico_id' => (int) $p->id,
                'nome' => $p->nome,
                'tipo' => $p->tipo,
            ])
            ->all();

        return [
            'top_5_por_receita' => $vendidos,
            'estagnados_no_periodo' => $estagnados,
            'dias_analisados' => $periodDays,
        ];
    }
}
