<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use Illuminate\Support\Facades\DB;

class GetSuppliersOverviewTool implements ToolInterface
{
    public function getName(): string
    {
        return 'get_suppliers_overview';
    }

    public function getDescription(): string
    {
        return 'Consulta a lista de fornecedores cadastrados na empresa do cliente (nome, categoria, telefone, e-mail). '
             . 'USE ESTA FERRAMENTA quando o usuário perguntar por fornecedores, contatos de parceiros de compras, '
             . 'insumos ou compras para reposição.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'categoria' => [
                    'type' => 'string',
                    'description' => 'Opcional. Categoria do fornecedor (ex: Alimentos, Embalagens, Bebidas, TI, Serviços).',
                ],
                'busca' => [
                    'type' => 'string',
                    'description' => 'Opcional. Busca por nome, CNPJ ou e-mail do fornecedor.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Opcional. Limite máximo de fornecedores para retornar (padrão 20, máx 50).',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $categoria = isset($arguments['categoria']) && trim((string) $arguments['categoria']) !== ''
            ? trim((string) $arguments['categoria'])
            : null;
        $busca = isset($arguments['busca']) && trim((string) $arguments['busca']) !== ''
            ? trim((string) $arguments['busca'])
            : null;
        $limit = isset($arguments['limit']) ? max(1, min(50, (int) $arguments['limit'])) : 20;

        $query = DB::table('fornecedores')
            ->where('cliente_id', $tenantId);

        if ($categoria !== null) {
            $query->where('categoria', $categoria);
        }

        if ($busca !== null) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('cnpj_cpf', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%");
            });
        }

        $totalCadastrados = (clone $query)->count();

        $fornecedores = $query
            ->orderBy('nome', 'asc')
            ->limit($limit)
            ->get([
                'id',
                'nome',
                'cnpj_cpf',
                'email',
                'telefone',
                'endereco',
                'categoria',
                'notas',
            ]);

        $formatados = $fornecedores->map(fn ($f) => [
            'id' => (int) $f->id,
            'nome' => $f->nome,
            'cnpj_cpf' => $f->cnpj_cpf,
            'categoria' => $f->categoria ?? 'Geral',
            'telefone' => $f->telefone,
            'email' => $f->email,
            'endereco' => $f->endereco,
            'notas' => $f->notas,
        ])->all();

        return [
            'total_encontrados' => $totalCadastrados,
            'listados' => count($formatados),
            'fornecedores' => $formatados,
        ];
    }
}
