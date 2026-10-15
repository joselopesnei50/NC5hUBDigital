<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\WhatsappBotInteraction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WhatsappFinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->query('periodo', '30d');
        [$start, $end, $label] = $this->resolvePeriod($periodo);

        $rows = DB::table('whatsapp_bot_interactions')
            ->selectRaw('cliente_id,
                COUNT(*) as msgs,
                SUM(cost) as cost_total,
                AVG(cost) as cost_medio,
                SUM(tokens_in) as tokens_in_total,
                SUM(tokens_out) as tokens_out_total,
                SUM(CASE WHEN failed = 1 THEN 1 ELSE 0 END) as failures,
                MAX(created_at) as ultima_atividade')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('cliente_id')
            ->orderByDesc('cost_total')
            ->get();

        $clienteIds = $rows->pluck('cliente_id')->all();
        $clientes = Cliente::whereIn('id', $clienteIds)
            ->get(['id', 'razao_social'])
            ->keyBy('id');

        $linhas = $rows->map(function ($row) use ($clientes) {
            $cliente = $clientes->get($row->cliente_id);
            $taxaErro = $row->msgs > 0 ? round(($row->failures / $row->msgs) * 100, 1) : 0;
            return [
                'cliente_id' => $row->cliente_id,
                'razao_social' => $cliente?->razao_social ?? "Cliente #{$row->cliente_id}",
                'msgs' => (int) $row->msgs,
                'cost_total' => (float) $row->cost_total,
                'cost_medio' => (float) $row->cost_medio,
                'tokens_total' => (int) ($row->tokens_in_total + $row->tokens_out_total),
                'taxa_erro' => $taxaErro,
                'ultima_atividade' => $row->ultima_atividade ? Carbon::parse($row->ultima_atividade) : null,
            ];
        });

        $totais = [
            'clientes_ativos' => $linhas->count(),
            'msgs' => $linhas->sum('msgs'),
            'cost_total' => $linhas->sum('cost_total'),
            'tokens_total' => $linhas->sum('tokens_total'),
        ];

        return view('admin.whatsapp_financeiro.index', [
            'linhas' => $linhas,
            'totais' => $totais,
            'periodo' => $periodo,
            'periodoLabel' => $label,
            'start' => $start,
            'end' => $end,
        ]);
    }

    protected function resolvePeriod(string $periodo): array
    {
        $now = now();
        switch ($periodo) {
            case 'mes_atual':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), 'Mês atual (' . $now->isoFormat('MMM/YYYY') . ')'];
            case 'mes_anterior':
                $prev = $now->copy()->subMonthNoOverflow();
                return [$prev->copy()->startOfMonth(), $prev->copy()->endOfMonth(), 'Mês anterior (' . $prev->isoFormat('MMM/YYYY') . ')'];
            case '30d':
            default:
                return [$now->copy()->subDays(30), $now->copy(), 'Últimos 30 dias'];
        }
    }
}
