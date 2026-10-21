<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\AgentKnowledge;
use App\Models\Cliente;
use App\Models\WhatsappBotTraining;

class SaveCompanyKnowledgeTool implements ToolInterface
{
    public function getName(): string
    {
        return 'save_company_knowledge';
    }

    public function getDescription(): string
    {
        return 'Salva ou atualiza um conhecimento, regra, política, FAQ ou diferencial na memória unificada da empresa (usada tanto pelo Bruce na Gestão quanto pelo Agente de Atendimento no WhatsApp). '
             . 'USE ESTA TOOL sempre que o gestor disser "aprenda que...", "anote que...", "lembre que...", "adicione no treinamento/FAQ/políticas do bot...", ou quando ensinar como a empresa funciona.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'titulo' => [
                    'type' => 'string',
                    'description' => 'Título curto e claro do conhecimento ou regra (ex: "Política de Entrega e Frete", "Regra de Desconto à Vista", "FAQ: Prazo de Garantia").',
                ],
                'conteudo' => [
                    'type' => 'string',
                    'description' => 'Conteúdo completo e estruturado da regra, política, procedimento ou resposta que os agentes devem seguir.',
                ],
                'categoria_bot' => [
                    'type' => 'string',
                    'enum' => [
                        'geral',
                        'politicas',
                        'faq_custom',
                        'regras_proibicoes',
                        'diferenciais',
                        'processo_producao',
                        'historia_negocio',
                        'horario_atendimento',
                        'tom_de_voz',
                    ],
                    'description' => 'Se este conhecimento também se aplica a uma seção específica do Bot de WhatsApp, indique qual para sincronizar automaticamente.',
                ],
            ],
            'required' => ['titulo', 'conteudo'],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $titulo = trim((string) ($arguments['titulo'] ?? ''));
        $conteudo = trim((string) ($arguments['conteudo'] ?? ''));
        $categoriaBot = trim((string) ($arguments['categoria_bot'] ?? 'geral'));

        if ($titulo === '' || $conteudo === '') {
            return ['error' => 'Título e conteúdo são obrigatórios para salvar o treinamento.'];
        }

        // 1. Salva ou atualiza na tabela agent_knowledge (Base Unificada do BruceOS)
        $item = AgentKnowledge::updateOrCreate(
            [
                'cliente_id' => $tenantId,
                'titulo' => $titulo,
            ],
            [
                'conteudo' => $conteudo,
                'ativo' => true,
            ]
        );

        // 2. Sincroniza automaticamente com o treinamento do WhatsApp Bot se pertencer a uma categoria específica
        $sincronizadoBot = false;
        $allowedBotFields = [
            'politicas',
            'faq_custom',
            'regras_proibicoes',
            'diferenciais',
            'processo_producao',
            'historia_negocio',
            'horario_atendimento',
            'tom_de_voz',
        ];

        if (in_array($categoriaBot, $allowedBotFields, true)) {
            $training = WhatsappBotTraining::firstOrNew(['cliente_id' => $tenantId]);

            if (in_array($categoriaBot, ['horario_atendimento', 'tom_de_voz'], true)) {
                $training->{$categoriaBot} = mb_substr($conteudo, 0, 255);
            } else {
                $atual = trim((string) ($training->{$categoriaBot} ?? ''));
                $blocoNovo = "• {$titulo}: {$conteudo}";
                if ($atual === '') {
                    $training->{$categoriaBot} = $blocoNovo;
                } elseif (!str_contains($atual, $conteudo)) {
                    $training->{$categoriaBot} = $atual . "\n\n" . $blocoNovo;
                }
            }

            $training->save();
            $sincronizadoBot = true;

            // Também sincroniza com o DNA do Cliente se for diferenciais ou tom_de_voz
            $cliente = Cliente::find($tenantId);
            if ($cliente) {
                if ($categoriaBot === 'tom_de_voz') {
                    $cliente->update(['tom_voz' => mb_substr($conteudo, 0, 100)]);
                } elseif ($categoriaBot === 'diferenciais' && empty($cliente->diferenciais_competitivos)) {
                    $cliente->update(['diferenciais_competitivos' => mb_substr($conteudo, 0, 2000)]);
                }
            }
        }

        return [
            'status' => 'sucesso',
            'knowledge_id' => $item->id,
            'titulo' => $item->titulo,
            'sincronizado_whatsapp_bot' => $sincronizadoBot,
            'categoria_sincronizada' => $categoriaBot,
            'mensagem' => 'Conhecimento gravado com sucesso na memória unificada do BruceOS e já ativo tanto na Gestão quanto no Atendimento via WhatsApp.',
        ];
    }
}
