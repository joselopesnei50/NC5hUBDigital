<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\Cliente;
use App\Models\ProdutoServico;
use Throwable;

class ConsultarCatalogoWhatsappTool implements ToolInterface
{
    public function getName(): string
    {
        return 'consultar_catalogo';
    }

    public function getDescription(): string
    {
        return 'Consulta o catálogo oficial de produtos e serviços do negócio com preços, descrições e unidades. '
            . 'USE sempre que o cliente perguntar valores, disponibilidades, cardápio ou detalhes técnicos de itens. '
            . 'Argumento opcional: termo_busca (palavra-chave como nome do produto ou categoria).';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'termo_busca' => [
                    'type' => 'string',
                    'description' => 'Termo para filtrar produtos/serviços pelo nome ou descrição. Opcional.',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        try {
            $cliente = Cliente::find($tenantId);
            if (!$cliente) {
                return ['success' => false, 'error' => 'Negócio não encontrado.'];
            }

            $query = $cliente->produtosServicos();

            $termo = trim((string) ($arguments['termo_busca'] ?? ''));
            if ($termo !== '') {
                $query->where(function ($q) use ($termo) {
                    $q->where('nome', 'like', "%{$termo}%")
                      ->orWhere('descricao', 'like', "%{$termo}%")
                      ->orWhere('tipo', 'like', "%{$termo}%");
                });
            }

            $itens = $query->limit(15)->get();

            if ($itens->isEmpty()) {
                return [
                    'success' => true,
                    'total' => 0,
                    'mensagem' => 'Nenhum produto ou serviço encontrado no catálogo para esta busca.',
                    'itens' => [],
                ];
            }

            $catalogoFormatado = $itens->map(function (ProdutoServico $p) {
                return [
                    'id' => $p->id,
                    'nome' => $p->nome,
                    'tipo' => $p->tipo, // 'produto' ou 'servico'
                    'preco' => 'R$ ' . number_format((float) $p->preco, 2, ',', '.'),
                    'unidade' => $p->unidade_medida ?? 'un',
                    'descricao' => $p->descricao,
                ];
            })->toArray();

            return [
                'success' => true,
                'total' => count($catalogoFormatado),
                'itens' => $catalogoFormatado,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => 'Falha ao consultar catálogo: ' . $e->getMessage(),
            ];
        }
    }
}
