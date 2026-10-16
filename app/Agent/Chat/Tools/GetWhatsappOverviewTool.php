<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Agent\Metrics\WhatsappAtendimentoMetric;
use App\Models\WhatsappChat;
use Illuminate\Support\Str;

class GetWhatsappOverviewTool implements ToolInterface
{
    public function __construct(
        private WhatsappAtendimentoMetric $metric
    ) {
    }

    public function getName(): string
    {
        return 'get_whatsapp_overview';
    }

    public function getDescription(): string
    {
        return 'Retorna o panorama do atendimento via WhatsApp e do Bot IA do negócio: '
             . 'status da conexão, status do bot, volume de interações, escalações para humano '
             . 'e a lista de conversas não lidas ou que pediram atendimento humano e estão aguardando resposta. '
             . 'USE ESTA TOOL quando o gestor perguntar "como está o WhatsApp?", "tem alguém esperando resposta?", '
             . '"quem pediu atendente humano?" ou "como está o desempenho do bot?".';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'period_days' => [
                    'type' => 'integer',
                    'description' => 'Janela em dias para as métricas do bot (padrão 30).',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $days = (int) ($arguments['period_days'] ?? 30);
        if ($days < 1) $days = 30;
        if ($days > 365) $days = 365;

        $resumo = $this->metric->calculate($tenantId, $days);

        // Prioriza conversas com mensagens não lidas (especialmente onde o bot foi pausado/escalado)
        $chatsPendentes = WhatsappChat::with(['latestMessage', 'clienteFinal'])
            ->where('cliente_id', $tenantId)
            ->where('is_group', false)
            ->where(function ($q) {
                $q->where('unread_count', '>', 0)
                  ->orWhere('is_bot_active', false);
            })
            ->orderByDesc('unread_count')
            ->orderByDesc('last_message_at')
            ->limit(15)
            ->get();

        $filaAtencao = $chatsPendentes->map(function (WhatsappChat $c) {
            return [
                'chat_id' => (int) $c->id,
                'contato' => $c->clienteFinal?->nome_responsavel ?: ($c->contact_name ?: ($c->contact_phone ?: 'Sem nome')),
                'telefone' => $c->contact_phone ?: $c->wa_id,
                'nao_lidas' => (int) $c->unread_count,
                'bot_ativo_na_conversa' => (bool) $c->is_bot_active,
                'status_atendimento' => (!$c->is_bot_active && $c->unread_count > 0)
                    ? 'aguardando_humano'
                    : ($c->unread_count > 0 ? 'mensagens_nao_lidas' : 'bot_pausado_sem_pendencia'),
                'ultima_mensagem_em' => $c->last_message_at?->format('d/m/Y H:i'),
                'ultima_mensagem_trecho' => $c->latestMessage?->content
                    ? Str::limit((string) $c->latestMessage->content, 100)
                    : null,
            ];
        })->values()->all();

        return [
            'metricas' => $resumo,
            'conversas_em_atencao' => $filaAtencao,
        ];
    }
}
