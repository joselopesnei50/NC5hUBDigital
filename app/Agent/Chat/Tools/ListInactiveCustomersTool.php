<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ListInactiveCustomersTool implements ToolInterface
{
    public function getName(): string
    {
        return 'list_inactive_customers';
    }

    public function getDescription(): string
    {
        return 'Lista até 20 clientes finais que compraram no passado mas ficaram sem comprar '
             . 'nos últimos N dias (default 60), com detalhes suficientes pra você redigir '
             . 'mensagem de reativação: nome, responsável, telefone, e-mail, última compra e '
             . 'total gasto histórico. USE ESTA TOOL quando o gestor pedir a LISTA COMPLETA '
             . 'de clientes sumidos, ou quando quiser gerar mensagem de reativação em lote. '
             . 'Ela é mais detalhada que o bloco "clientes_sumidos" do snapshot (que traz só '
             . '10 exemplos sem histórico financeiro).';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'days' => [
                    'type' => 'integer',
                    'description' => 'Janela em dias sem compra para considerar sumido (padrão 60).',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Máximo de clientes a retornar (padrão 10, teto 20).',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $days = (int) ($arguments['days'] ?? 60);
        if ($days < 1) $days = 60;
        if ($days > 365) $days = 365;

        $limit = (int) ($arguments['limit'] ?? 10);
        if ($limit < 1) $limit = 10;
        if ($limit > 20) $limit = 20;

        $cutoff = Carbon::now()->subDays($days)->startOfDay();

        // IDs que tiveram pedido ANTES do cutoff (tem historico)
        $comHistorico = DB::table('pedidos_clientes')
            ->where('cliente_id', $tenantId)
            ->whereNotIn('status', ['Cancelado'])
            ->where('created_at', '<', $cutoff)
            ->distinct()
            ->pluck('cliente_final_id');

        if ($comHistorico->isEmpty()) {
            return [
                'quantidade' => 0,
                'clientes' => [],
                'dias_considerados' => $days,
            ];
        }

        // Sem pedidos dentro do periodo
        $ativosRecentes = DB::table('pedidos_clientes')
            ->where('cliente_id', $tenantId)
            ->whereNotIn('status', ['Cancelado'])
            ->where('created_at', '>=', $cutoff)
            ->distinct()
            ->pluck('cliente_final_id');

        $sumidosIds = $comHistorico->diff($ativosRecentes);

        if ($sumidosIds->isEmpty()) {
            return [
                'quantidade' => 0,
                'clientes' => [],
                'dias_considerados' => $days,
            ];
        }

        // Buscar detalhes + agregados de pedidos historicos
        $clientes = DB::table('clientes_finais as c')
            ->leftJoin('pedidos_clientes as p', function ($join) use ($tenantId) {
                $join->on('p.cliente_final_id', '=', 'c.id')
                     ->where('p.cliente_id', '=', $tenantId)
                     ->whereNotIn('p.status', ['Cancelado']);
            })
            ->leftJoin('pedido_cliente_itens as i', 'i.pedido_cliente_id', '=', 'p.id')
            ->whereIn('c.id', $sumidosIds->all())
            ->where('c.cliente_id', $tenantId)
            ->groupBy('c.id', 'c.nome_empresa', 'c.nome_responsavel', 'c.telefone', 'c.email', 'c.cidade')
            ->select(
                'c.id',
                'c.nome_empresa',
                'c.nome_responsavel',
                'c.telefone',
                'c.email',
                'c.cidade',
                DB::raw('MAX(p.created_at) as ultima_compra'),
                DB::raw('SUM(i.valor_total) as total_gasto_historico'),
                DB::raw('COUNT(DISTINCT p.id) as qtd_pedidos_historicos')
            )
            ->orderByDesc('total_gasto_historico')
            ->limit($limit)
            ->get()
            ->map(fn ($c) => [
                'cliente_final_id' => (int) $c->id,
                'nome_empresa' => $c->nome_empresa,
                'nome_responsavel' => $c->nome_responsavel,
                'telefone' => $c->telefone,
                'email' => $c->email,
                'cidade' => $c->cidade,
                'ultima_compra' => $c->ultima_compra,
                'total_gasto_historico_brl' => (float) ($c->total_gasto_historico ?? 0),
                'qtd_pedidos_historicos' => (int) ($c->qtd_pedidos_historicos ?? 0),
            ])
            ->all();

        return [
            'quantidade' => count($clientes),
            'clientes' => $clientes,
            'dias_considerados' => $days,
        ];
    }
}
