<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use Illuminate\Support\Facades\DB;

class GetProductsCatalogTool implements ToolInterface
{
    public function getName(): string
    {
        return 'get_products_catalog';
    }

    public function getDescription(): string
    {
        return 'Consulta o cardápio, catálogo de produtos e serviços cadastrados no negócio do cliente (preços, unidades, categorias). '
             . 'USE ESTA FERRAMENTA quando o usuário perguntar sobre o cardápio, produtos à venda, preços vigentes de itens ou serviços oferecidos.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'busca' => [
                    'type' => 'string',
                    'description' => 'Opcional. Termo de busca por nome ou descrição do produto/serviço (ex: "X-Burger", "Refrigerante", "Consultoria").',
                ],
                'tipo' => [
                    'type' => 'string',
                    'description' => 'Opcional. Filtro por tipo: "produto" ou "servico". Se omitido, retorna ambos.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Opcional. Limite máximo de itens a listar (padrão 20, máx 50).',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $busca = isset($arguments['busca']) && trim((string) $arguments['busca']) !== ''
            ? trim((string) $arguments['busca'])
            : null;
        $tipo = isset($arguments['tipo']) && trim((string) $arguments['tipo']) !== ''
            ? trim((string) $arguments['tipo'])
            : null;
        $limit = isset($arguments['limit']) ? max(1, min(50, (int) $arguments['limit'])) : 20;

        $query = DB::table('produtos_servicos')
            ->where('cliente_id', $tenantId);

        if ($tipo !== null) {
            $query->where('tipo', $tipo);
        }

        if ($busca !== null) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('descricao', 'like', "%{$busca}%");
            });
        }

        $totalCadastrados = (clone $query)->count();

        $itens = $query
            ->orderBy('nome', 'asc')
            ->limit($limit)
            ->get([
                'id',
                'tipo',
                'nome',
                'descricao',
                'preco_padrao',
                'unidade_medida',
            ]);

        $formatados = $itens->map(fn ($p) => [
            'id' => (int) $p->id,
            'tipo' => $p->tipo,
            'nome' => $p->nome,
            'descricao' => $p->descricao,
            'preco' => (float) $p->preco_padrao,
            'unidade' => $p->unidade_medida ?? 'un',
        ])->all();

        return [
            'total_encontrados' => $totalCadastrados,
            'listados' => count($formatados),
            'itens' => $formatados,
        ];
    }
}
