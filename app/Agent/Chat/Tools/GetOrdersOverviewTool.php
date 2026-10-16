<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GetOrdersOverviewTool implements ToolInterface
{
    public function getName(): string
    {
        return 'get_orders_overview';
    }

    public function getDescription(): string
    {
        return 'Consulta pedidos de clientes/vendas realizadas pelo negócio do cliente (hamburgueria, loja, serviços). '
             . 'Retorna métricas consolidadas (total de pedidos, faturamento, ticket médio, pedidos por status) '
             . 'e lista detalhada dos pedidos mais recentes com seus itens, valores e clientes. '
             . 'USE ESTA FERRAMENTA quando o usuário perguntar sobre pedidos, vendas, delivery, status de entrega ou ticket médio.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'description' => 'Opcional. Filtra por status: "Pendente", "Em Produção", "Em Andamento", "Concluído", "Entregue", "Aprovado", "Cancelado". Se omitido, traz todos.',
                ],
                'period_days' => [
                    'type' => 'integer',
                    'description' => 'Opcional. Janela em dias passados (padrão 30, ex: 1 para hoje, 7 para a semana, 30 para o mês).',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Opcional. Quantidade máxima de pedidos para listar detalhadamente (padrão 10, máx 30).',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $status = isset($arguments['status']) && trim((string) $arguments['status']) !== '' 
            ? trim((string) $arguments['status']) 
            : null;
        $periodDays = isset($arguments['period_days']) ? max(1, min(365, (int) $arguments['period_days'])) : 30;
        $limit = isset($arguments['limit']) ? max(1, min(30, (int) $arguments['limit'])) : 10;

        $startDate = Carbon::now()->subDays($periodDays)->startOfDay();

        // Query base isolada por tenant
        $baseQuery = DB::table('pedidos_clientes as p')
            ->where('p.cliente_id', $tenantId)
            ->where('p.created_at', '>=', $startDate);

        if ($status !== null) {
            $baseQuery->where('p.status', $status);
        }

        // Métricas de resumo
        $totalPedidos = (clone $baseQuery)->count();
        $valorTotal = (float) (clone $baseQuery)->whereNotIn('p.status', ['Cancelado'])->sum('p.valor');
        $ticketMedio = $totalPedidos > 0 ? round($valorTotal / $totalPedidos, 2) : 0.0;

        // Distribuição por status
        $statusCounts = (clone $baseQuery)
            ->select('p.status', DB::raw('COUNT(*) as total'))
            ->groupBy('p.status')
            ->pluck('total', 'status')
            ->toArray();

        // Pedidos recentes
        $pedidos = (clone $baseQuery)
            ->leftJoin('clientes_finais as cf', 'cf.id', '=', 'p.cliente_final_id')
            ->orderByDesc('p.created_at')
            ->limit($limit)
            ->select([
                'p.id',
                'p.titulo',
                'p.status',
                'p.valor',
                'p.data_pedido',
                'p.data_entrega',
                'p.created_at',
                'cf.nome as cliente_nome',
                'cf.telefone as cliente_telefone',
            ])
            ->get();

        // Coleta itens dos pedidos listados
        $pedidoIds = $pedidos->pluck('id')->all();
        $itensGrouped = [];

        if (!empty($pedidoIds)) {
            $itens = DB::table('pedido_cliente_itens')
                ->whereIn('pedido_cliente_id', $pedidoIds)
                ->select([
                    'pedido_cliente_id',
                    'nome_item',
                    'quantidade',
                    'valor_unitario',
                    'valor_total',
                ])
                ->get();

            foreach ($itens as $item) {
                $itensGrouped[$item->pedido_cliente_id][] = [
                    'item' => $item->nome_item,
                    'qtd' => (float) $item->quantidade,
                    'unitario' => (float) $item->valor_unitario,
                    'total' => (float) $item->valor_total,
                ];
            }
        }

        $listaFormatada = $pedidos->map(function ($p) use ($itensGrouped) {
            return [
                'id' => (int) $p->id,
                'titulo' => $p->titulo,
                'status' => $p->status,
                'valor' => (float) $p->valor,
                'cliente' => $p->cliente_nome ?? 'Cliente Avulso',
                'telefone' => $p->cliente_telefone,
                'data_pedido' => $p->data_pedido,
                'data_entrega' => $p->data_entrega,
                'itens' => $itensGrouped[$p->id] ?? [],
            ];
        })->all();

        return [
            'periodo_dias' => $periodDays,
            'resumo' => [
                'total_pedidos' => $totalPedidos,
                'faturamento_total' => $valorTotal,
                'ticket_medio' => $ticketMedio,
                'por_status' => $statusCounts,
            ],
            'pedidos_recentes' => $listaFormatada,
        ];
    }
}
