<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use Illuminate\Support\Facades\DB;

class GetDocumentsOverviewTool implements ToolInterface
{
    public function getName(): string
    {
        return 'get_documents_overview';
    }

    public function getDescription(): string
    {
        return 'Consulta a lista de documentos e arquivos salvos no cofre do cliente (contratos, notas fiscais, relatórios). '
             . 'USE ESTA FERRAMENTA quando o usuário perguntar quais documentos tem cadastrados, se há contratos anexados ou arquivos disponíveis.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'tipo' => [
                    'type' => 'string',
                    'description' => 'Opcional. Tipo de documento (ex: "contrato", "nota_fiscal", "relatorio", "outro").',
                ],
                'busca' => [
                    'type' => 'string',
                    'description' => 'Opcional. Termo de busca no nome do documento.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Opcional. Limite máximo de documentos (padrão 20, máx 50).',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $tipo = isset($arguments['tipo']) && trim((string) $arguments['tipo']) !== ''
            ? trim((string) $arguments['tipo'])
            : null;
        $busca = isset($arguments['busca']) && trim((string) $arguments['busca']) !== ''
            ? trim((string) $arguments['busca'])
            : null;
        $limit = isset($arguments['limit']) ? max(1, min(50, (int) $arguments['limit'])) : 20;

        $query = DB::table('documentos')
            ->where('cliente_id', $tenantId);

        if ($tipo !== null) {
            $query->where('tipo', $tipo);
        }

        if ($busca !== null) {
            $query->where('nome', 'like', "%{$busca}%");
        }

        $totalCadastrados = (clone $query)->count();

        $docs = $query
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get([
                'id',
                'nome',
                'tipo',
                'enviado_por',
                'created_at',
            ]);

        $formatados = $docs->map(fn ($d) => [
            'id' => (int) $d->id,
            'nome' => $d->nome,
            'tipo' => $d->tipo ?? 'outro',
            'enviado_por' => $d->enviado_por,
            'data_envio' => $d->created_at,
        ])->all();

        return [
            'total_documentos' => $totalCadastrados,
            'listados' => count($formatados),
            'documentos' => $formatados,
        ];
    }
}
