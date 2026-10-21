<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\ClienteFinal;
use App\Models\PedidoCliente;
use App\Models\PedidoClienteItem;
use App\Models\ProdutoServico;
use App\Models\WhatsappChat;
use Illuminate\Support\Facades\DB;
use Throwable;

class CriarOrcamentoWhatsappTool implements ToolInterface
{
    public function getName(): string
    {
        return 'criar_orcamento_pedido';
    }

    public function getDescription(): string
    {
        return 'Cria um orçamento ou pedido comercial no sistema BruceOS para o cliente que está conversando no WhatsApp e retorna o link público da proposta para aprovação online. '
            . 'USE quando o cliente pedir um orçamento formal, quiser fechar um pedido ou solicitar uma proposta com itens do catálogo. '
            . 'Argumentos: titulo (ex: "Orçamento de Serviços"), descricao (opcional), itens (lista de objetos com produto_servico_id ou nome_item, quantidade e valor_unitario).';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'titulo' => [
                    'type' => 'string',
                    'description' => 'Título curto do orçamento ou pedido (ex: "Proposta Comercial - Kit X").',
                ],
                'nome_cliente' => [
                    'type' => 'string',
                    'description' => 'Nome do cliente ou empresa caso ainda não esteja cadastrado.',
                ],
                'descricao' => [
                    'type' => 'string',
                    'description' => 'Observações ou condições acordadas na conversa.',
                ],
                'itens' => [
                    'type' => 'array',
                    'description' => 'Lista de produtos ou serviços incluídos no orçamento.',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'produto_servico_id' => [
                                'type' => 'integer',
                                'description' => 'ID do item retornado por consultar_catalogo (opcional).',
                            ],
                            'nome_item' => [
                                'type' => 'string',
                                'description' => 'Nome do produto ou serviço.',
                            ],
                            'quantidade' => [
                                'type' => 'number',
                                'description' => 'Quantidade solicitada (default: 1).',
                            ],
                            'valor_unitario' => [
                                'type' => 'number',
                                'description' => 'Valor unitário em reais (se omitido e produto_servico_id informado, puxa do catálogo).',
                            ],
                        ],
                        'required' => ['nome_item', 'quantidade'],
                    ],
                ],
            ],
            'required' => ['titulo', 'itens'],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $chatId = (int) ($arguments['_chat_id'] ?? 0);
        if ($chatId < 1) {
            return ['ok' => false, 'erro' => 'Contexto do chat ausente.'];
        }

        $chat = WhatsappChat::where('id', $chatId)->where('cliente_id', $tenantId)->first();
        if (!$chat) {
            return ['ok' => false, 'erro' => 'Chat não encontrado.'];
        }

        $itensInput = $arguments['itens'] ?? [];
        if (empty($itensInput) || !is_array($itensInput)) {
            return ['ok' => false, 'erro' => 'Informe ao menos 1 item para gerar o orçamento.'];
        }

        try {
            return DB::transaction(function () use ($tenantId, $chat, $arguments, $itensInput) {
                $clienteFinalId = $chat->cliente_final_id;

                if (!$clienteFinalId) {
                    $nome = trim((string) ($arguments['nome_cliente'] ?? $chat->contact_name ?: 'Cliente WhatsApp'));
                    $telefone = $chat->contact_phone ?: preg_replace('/@.*$/', '', (string) $chat->wa_id);

                    $cf = ClienteFinal::firstOrCreate(
                        [
                            'cliente_id' => $tenantId,
                            'telefone' => $telefone,
                        ],
                        [
                            'nome_empresa' => $nome,
                            'nome_responsavel' => $nome,
                        ]
                    );

                    $clienteFinalId = $cf->id;
                    $chat->update(['cliente_final_id' => $clienteFinalId]);
                }

                $valorTotalPedido = 0.0;
                $itensParaInserir = [];

                foreach ($itensInput as $item) {
                    $prodId = !empty($item['produto_servico_id']) ? (int) $item['produto_servico_id'] : null;
                    $qtd = max(0.01, (float) ($item['quantidade'] ?? 1));
                    $valorUnit = isset($item['valor_unitario']) ? (float) $item['valor_unitario'] : null;
                    $nomeItem = trim((string) ($item['nome_item'] ?? 'Item'));

                    if ($prodId) {
                        $produto = ProdutoServico::where('cliente_id', $tenantId)->find($prodId);
                        if ($produto) {
                            $nomeItem = $produto->nome;
                            if ($valorUnit === null || $valorUnit <= 0) {
                                $valorUnit = (float) $produto->preco_padrao;
                            }
                        } else {
                            $prodId = null;
                        }
                    }

                    $valorUnit = max(0.0, (float) $valorUnit);
                    $subtotal = round($qtd * $valorUnit, 2);
                    $valorTotalPedido += $subtotal;

                    $itensParaInserir[] = [
                        'produto_servico_id' => $prodId,
                        'nome_item' => $nomeItem,
                        'quantidade' => $qtd,
                        'valor_unitario' => $valorUnit,
                        'valor_total' => $subtotal,
                    ];
                }

                $pedido = PedidoCliente::create([
                    'cliente_id' => $tenantId,
                    'cliente_final_id' => $clienteFinalId,
                    'titulo' => mb_substr(trim((string) $arguments['titulo']), 0, 255),
                    'descricao' => trim((string) ($arguments['descricao'] ?? '')) ?: null,
                    'valor' => $valorTotalPedido,
                    'status' => 'Orçamento',
                    'data_pedido' => now()->toDateString(),
                ]);

                foreach ($itensParaInserir as $row) {
                    $row['pedido_cliente_id'] = $pedido->id;
                    PedidoClienteItem::create($row);
                }

                $linkProposta = route('public.pedido.show', $pedido->token_publico);

                return [
                    'ok' => true,
                    'pedido_id' => $pedido->id,
                    'valor_total' => 'R$ ' . number_format($valorTotalPedido, 2, ',', '.'),
                    'link_proposta' => $linkProposta,
                    'instrucao' => 'Envie o link_proposta para o cliente na conversa para que ele possa visualizar e aprovar o orçamento online.',
                ];
            });
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'erro' => 'Erro ao gerar orçamento: ' . $e->getMessage(),
            ];
        }
    }
}
