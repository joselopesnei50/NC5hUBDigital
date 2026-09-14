<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use Illuminate\Support\Facades\DB;

class ListPendingTasksTool implements ToolInterface
{
    public function getName(): string
    {
        return 'list_pending_tasks';
    }

    public function getDescription(): string
    {
        return 'Lista tarefas ainda NÃO concluídas dos projetos ativos do gestor '
             . '(projetos com status "pendente", "em_andamento" ou "aguardando_cliente"), '
             . 'agrupadas por projeto. Traz até 30 tarefas. USE ESTA TOOL quando o gestor '
             . 'perguntar "o que tá pendente?", "quais tarefas ainda faltam?", '
             . '"como estão meus projetos?" ou variantes.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'projeto_id' => [
                    'type' => 'integer',
                    'description' => 'Opcional. Filtra por um projeto específico. Se omitido, traz de todos os projetos ativos.',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $projetoId = isset($arguments['projeto_id']) ? (int) $arguments['projeto_id'] : null;

        $query = DB::table('projeto_tarefas as t')
            ->join('projetos as p', 'p.id', '=', 't.projeto_id')
            ->where('p.cliente_id', $tenantId)
            ->where('t.concluida', false)
            ->whereIn('p.status', ['pendente', 'em_andamento', 'aguardando_cliente']);

        if ($projetoId !== null && $projetoId > 0) {
            $query->where('p.id', $projetoId);
        }

        $tarefas = $query
            ->orderBy('p.id')
            ->orderBy('t.id')
            ->limit(30)
            ->get([
                't.id as tarefa_id',
                't.titulo as tarefa',
                't.created_at as tarefa_criada',
                'p.id as projeto_id',
                'p.nome as projeto',
                'p.status as projeto_status',
                'p.data_previsao',
            ]);

        // Agrupar por projeto para o LLM entender a estrutura
        $porProjeto = [];
        foreach ($tarefas as $t) {
            $key = 'projeto_' . $t->projeto_id;
            if (!isset($porProjeto[$key])) {
                $porProjeto[$key] = [
                    'projeto_id' => (int) $t->projeto_id,
                    'projeto' => $t->projeto,
                    'status' => $t->projeto_status,
                    'data_previsao' => $t->data_previsao,
                    'tarefas' => [],
                ];
            }
            $porProjeto[$key]['tarefas'][] = [
                'tarefa_id' => (int) $t->tarefa_id,
                'titulo' => $t->tarefa,
                'criada_em' => $t->tarefa_criada,
            ];
        }

        return [
            'total_projetos' => count($porProjeto),
            'total_tarefas' => $tarefas->count(),
            'projetos' => array_values($porProjeto),
        ];
    }
}
