<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InactiveCustomersMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'clientes_sumidos';
    }

    /**
     * Clientes finais que NAO compraram dentro do periodo analisado
     * (mas ja compraram alguma vez antes). Isso mostra queda de recorrencia.
     */
    public function calculate(int $tenantId, int $periodDays): array
    {
        $cutoff = Carbon::now()->subDays($periodDays)->startOfDay();

        // IDs de clientes finais que TIVERAM ao menos um pedido antes do cutoff
        $comHistorico = DB::table('pedidos_clientes')
            ->where('cliente_id', $tenantId)
            ->whereNotIn('status', ['Cancelado'])
            ->where('created_at', '<', $cutoff)
            ->distinct()
            ->pluck('cliente_final_id');

        if ($comHistorico->isEmpty()) {
            return [
                'quantidade_sumidos' => 0,
                'exemplos' => [],
                'dias_analisados' => $periodDays,
            ];
        }

        // Dessas, quais NAO tiveram pedidos no periodo recente
        $ativosRecentes = DB::table('pedidos_clientes')
            ->where('cliente_id', $tenantId)
            ->whereNotIn('status', ['Cancelado'])
            ->where('created_at', '>=', $cutoff)
            ->distinct()
            ->pluck('cliente_final_id');

        $sumidosIds = $comHistorico->diff($ativosRecentes);

        if ($sumidosIds->isEmpty()) {
            return [
                'quantidade_sumidos' => 0,
                'exemplos' => [],
                'dias_analisados' => $periodDays,
            ];
        }

        $exemplos = DB::table('clientes_finais')
            ->whereIn('id', $sumidosIds->all())
            ->where('cliente_id', $tenantId)
            ->select('id', 'nome_empresa', 'nome_responsavel', 'telefone', 'email')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'cliente_final_id' => (int) $c->id,
                'nome' => $c->nome_empresa,
                'responsavel' => $c->nome_responsavel,
                'telefone' => $c->telefone,
                'email' => $c->email,
            ])
            ->all();

        return [
            'quantidade_sumidos' => $sumidosIds->count(),
            'exemplos' => $exemplos,
            'dias_analisados' => $periodDays,
        ];
    }
}
