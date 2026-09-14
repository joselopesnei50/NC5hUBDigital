<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use Illuminate\Support\Facades\DB;

class SearchCompanyKnowledgeTool implements ToolInterface
{
    public function getName(): string
    {
        return 'search_company_knowledge';
    }

    public function getDescription(): string
    {
        return 'Busca na base de conhecimento interna da empresa do gestor '
             . '(documentos que ele cadastrou em Base de Conhecimento — procedimentos, '
             . 'políticas, informações sobre produtos, preços, forma de trabalho etc.). '
             . 'USE ESTA TOOL quando o gestor perguntar algo que só pode ser respondido '
             . 'sabendo COMO A EMPRESA DELE FUNCIONA — por exemplo: "quanto cobramos por X", '
             . '"qual nossa política de troca", "como fazemos a entrega em Y", "qual o prazo '
             . 'padrão para Z". Retorna até 5 trechos mais relevantes. Se não achar nada, '
             . 'sugira ao gestor cadastrar o assunto em Base de Conhecimento.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Termo ou frase curta para buscar (2 a 10 palavras). Use as palavras-chave da pergunta do gestor, não a frase completa.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Máximo de trechos a retornar (padrão 3, teto 5).',
                ],
            ],
            'required' => ['query'],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $query = trim((string) ($arguments['query'] ?? ''));
        if ($query === '') {
            return ['error' => 'query é obrigatório.'];
        }

        $limit = (int) ($arguments['limit'] ?? 3);
        if ($limit < 1) $limit = 3;
        if ($limit > 5) $limit = 5;

        // Tokeniza em termos de >=3 chars pra rankear por matches somados.
        // LIKE simples: previsivel, funciona em qualquer collation, sem depender
        // de FULLTEXT ou stopwords do MySQL.
        $termos = array_filter(
            preg_split('/\s+/u', mb_strtolower($query)) ?: [],
            fn ($t) => mb_strlen($t) >= 3
        );

        if (empty($termos)) {
            return [
                'quantidade' => 0,
                'trechos' => [],
                'aviso' => 'Nenhum termo de busca útil (mínimo 3 caracteres por palavra).',
            ];
        }

        $q = DB::table('agent_knowledge')
            ->where('cliente_id', $tenantId)
            ->where('ativo', true);

        // OR (titulo LIKE ? OR conteudo LIKE ?) por termo
        $q->where(function ($outer) use ($termos) {
            foreach ($termos as $termo) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $termo) . '%';
                $outer->orWhere('titulo', 'LIKE', $like)
                      ->orWhere('conteudo', 'LIKE', $like);
            }
        });

        // Precisa buscar tudo pra rankear em PHP (SQL rank multi-term ficaria complexo).
        // Teto: 50 documentos considerados por chamada.
        $docs = $q->limit(50)->get(['id', 'titulo', 'conteudo']);

        if ($docs->isEmpty()) {
            return [
                'quantidade' => 0,
                'trechos' => [],
                'sugestao_ao_gestor' => 'Nada encontrado. Sugira ao gestor cadastrar o assunto em "Base de Conhecimento" no painel para você conseguir responder no futuro.',
            ];
        }

        $ranked = $docs->map(function ($doc) use ($termos) {
            $tituloLower = mb_strtolower($doc->titulo);
            $conteudoLower = mb_strtolower($doc->conteudo);
            $score = 0;
            foreach ($termos as $termo) {
                // Titulo pesa 3x
                $score += substr_count($tituloLower, $termo) * 3;
                $score += substr_count($conteudoLower, $termo);
            }
            return [
                'doc' => $doc,
                'score' => $score,
            ];
        })
        ->filter(fn ($r) => $r['score'] > 0)
        ->sortByDesc('score')
        ->take($limit)
        ->values();

        $trechos = $ranked->map(function ($r) {
            $conteudo = $r['doc']->conteudo;
            // Resumo: primeiros ~400 chars — suficiente pro Bruce compor resposta
            $resumo = mb_strlen($conteudo) > 400
                ? mb_substr($conteudo, 0, 400) . '…'
                : $conteudo;
            return [
                'doc_id' => (int) $r['doc']->id,
                'titulo' => $r['doc']->titulo,
                'trecho' => $resumo,
            ];
        })->all();

        return [
            'quantidade' => count($trechos),
            'trechos' => $trechos,
        ];
    }
}
