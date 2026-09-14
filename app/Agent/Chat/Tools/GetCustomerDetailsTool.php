<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use Illuminate\Support\Facades\DB;

class GetCustomerDetailsTool implements ToolInterface
{
    public function getName(): string
    {
        return 'get_customer_details';
    }

    public function getDescription(): string
    {
        return 'Retorna o perfil completo de UM cliente final específico da empresa do gestor: '
             . 'dados de contato, quantidade de pedidos, ticket médio, total gasto histórico, '
             . 'data da última compra e os 5 pedidos mais recentes. '
             . 'USE ESTA TOOL quando o gestor perguntar sobre um cliente específico pelo nome ou ID '
             . '(ex: "e o cliente X?", "me mostra o cliente do id 12"). '
             . 'Requer cliente_final_id — se o gestor mencionar só o nome, peça confirmação do ID '
             . 'antes de chamar (você pode ter visto o ID no snapshot).';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'cliente_final_id' => [
                    'type' => 'integer',
                    'description' => 'ID numérico do cliente final na base do gestor.',
                ],
            ],
            'required' => ['cliente_final_id'],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $clienteFinalId = (int) ($arguments['cliente_final_id'] ?? 0);
        if ($clienteFinalId < 1) {
            return ['error' => 'cliente_final_id é obrigatório e deve ser um inteiro válido.'];
        }

        $cliente = DB::table('clientes_finais')
            ->where('id', $clienteFinalId)
            ->where('cliente_id', $tenantId)
            ->first();

        if (!$cliente) {
            return ['error' => 'Cliente final não encontrado na sua base.'];
        }

        $agregados = DB::table('pedidos_clientes as p')
            ->join('pedido_cliente_itens as i', 'i.pedido_cliente_id', '=', 'p.id')
            ->where('p.cliente_id', $tenantId)
            ->where('p.cliente_final_id', $clienteFinalId)
            ->whereNotIn('p.status', ['Cancelado'])
            ->select(
                DB::raw('COUNT(DISTINCT p.id) as qtd_pedidos'),
                DB::raw('SUM(i.valor_total) as total_gasto'),
                DB::raw('MAX(p.created_at) as ultima_compra')
            )
            ->first();

        $qtdPedidos = (int) ($agregados->qtd_pedidos ?? 0);
        $totalGasto = (float) ($agregados->total_gasto ?? 0);
        $ticketMedio = $qtdPedidos > 0 ? round($totalGasto / $qtdPedidos, 2) : 0;

        $ultimos = DB::table('pedidos_clientes')
            ->where('cliente_id', $tenantId)
            ->where('cliente_final_id', $clienteFinalId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'titulo', 'valor', 'status', 'data_pedido', 'data_entrega'])
            ->map(fn ($p) => [
                'pedido_id' => (int) $p->id,
                'titulo' => $p->titulo,
                'valor_brl' => (float) $p->valor,
                'status' => $p->status,
                'data_pedido' => $p->data_pedido,
                'data_entrega' => $p->data_entrega,
            ])
            ->all();

        return [
            'perfil' => [
                'cliente_final_id' => (int) $cliente->id,
                'nome_empresa' => $cliente->nome_empresa,
                'nome_responsavel' => $cliente->nome_responsavel,
                'telefone' => $cliente->telefone,
                'email' => $cliente->email,
                'cidade' => $cliente->cidade,
                'aniversario_empresa' => $cliente->data_aniversario_empresa,
                'aniversario_responsavel' => $cliente->data_aniversario_responsavel,
            ],
            'historico' => [
                'qtd_pedidos_validos' => $qtdPedidos,
                'total_gasto_brl' => $totalGasto,
                'ticket_medio_brl' => $ticketMedio,
                'ultima_compra' => $agregados->ultima_compra,
            ],
            'ultimos_pedidos' => $ultimos,
        ];
    }
}
