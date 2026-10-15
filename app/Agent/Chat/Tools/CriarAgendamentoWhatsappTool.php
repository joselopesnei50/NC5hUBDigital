<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\Appointment;
use App\Models\AppointmentResource;
use App\Models\WhatsappBotTraining;
use App\Models\WhatsappChat;
use App\Services\AppointmentScheduler;
use Illuminate\Support\Carbon;

class CriarAgendamentoWhatsappTool implements ToolInterface
{
    public function getName(): string
    {
        return 'criar_agendamento';
    }

    public function getDescription(): string
    {
        return 'Cria um novo agendamento no calendario do negocio para o cliente final que esta conversando. '
            . 'USE SOMENTE depois de o cliente ter CONFIRMADO expressamente o dia e horario propostos. '
            . 'Nao use essa tool pra consultar — ela cria. '
            . 'Argumentos: data_hora (formato YYYY-MM-DD HH:MM no fuso de Sao Paulo), '
            . 'duracao_minutos (opcional, usa duracao padrao do negocio quando omitido), '
            . 'titulo (resumo curto, ex: "Corte feminino", "Visita apartamento Centro"), '
            . 'descricao (opcional, detalhes do que vai ser feito).';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'data_hora' => [
                    'type' => 'string',
                    'description' => 'Data e hora no formato YYYY-MM-DD HH:MM (horario local SP). Deve ser futura.',
                ],
                'duracao_minutos' => [
                    'type' => 'integer',
                    'description' => 'Duracao em minutos. Omita pra usar duracao padrao do negocio.',
                ],
                'titulo' => [
                    'type' => 'string',
                    'description' => 'Resumo curto do agendamento. Max 160 chars.',
                ],
                'descricao' => [
                    'type' => 'string',
                    'description' => 'Detalhes adicionais do atendimento.',
                ],
                'resource_id' => [
                    'type' => 'integer',
                    'description' => 'ID do recurso (profissional/cadeira/etc). Obrigatorio quando o negocio tem 2+ recursos ativos. Use listar_recursos pra obter. Passe 0 (zero) se o cliente disser "qualquer um" ou "tanto faz" — o sistema aloca o primeiro disponivel.',
                ],
            ],
            'required' => ['data_hora', 'titulo'],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $chatId = (int) ($arguments['_chat_id'] ?? 0);
        if ($chatId < 1) {
            return ['ok' => false, 'erro' => 'contexto do chat ausente — nao e possivel criar agendamento.'];
        }

        $chat = WhatsappChat::where('id', $chatId)->where('cliente_id', $tenantId)->first();
        if (!$chat) {
            return ['ok' => false, 'erro' => 'chat nao encontrado.'];
        }

        $dataHoraStr = (string) ($arguments['data_hora'] ?? '');
        try {
            $when = Carbon::createFromFormat('Y-m-d H:i', $dataHoraStr);
        } catch (\Throwable $e) {
            return ['ok' => false, 'erro' => "data_hora invalida ({$dataHoraStr}). Use YYYY-MM-DD HH:MM."];
        }

        if ($when->isPast()) {
            return ['ok' => false, 'erro' => "a data informada ja passou. Combine um horario futuro."];
        }

        $duracao = (int) ($arguments['duracao_minutos'] ?? ($arguments['_default_duration'] ?? 60));
        $duracao = max(5, min(1440, $duracao));

        $titulo = trim((string) ($arguments['titulo'] ?? ''));
        if ($titulo === '') {
            return ['ok' => false, 'erro' => 'titulo obrigatorio.'];
        }
        $titulo = mb_substr($titulo, 0, 160);

        $descricao = trim((string) ($arguments['descricao'] ?? '')) ?: null;

        $training = WhatsappBotTraining::where('cliente_id', $tenantId)->first();
        $scheduler = app(AppointmentScheduler::class);

        $resourceIdInput = array_key_exists('resource_id', $arguments) ? (int) $arguments['resource_id'] : null;
        $activeResources = AppointmentResource::where('cliente_id', $tenantId)->active()->ordered()->get();

        $resolvedResourceId = null;
        if ($activeResources->isNotEmpty()) {
            if ($resourceIdInput === null) {
                if ($activeResources->count() === 1) {
                    $resolvedResourceId = $activeResources->first()->id;
                } else {
                    return [
                        'ok' => false,
                        'erro' => 'escolha_recurso_necessaria',
                        'mensagem' => "O negocio tem mais de um recurso ativo. Pergunte ao cliente qual ele quer antes de criar.",
                        'recursos' => $activeResources->map(fn ($r) => ['id' => $r->id, 'nome' => $r->nome])->values()->all(),
                    ];
                }
            } elseif ($resourceIdInput === 0) {
                $resolvedResourceId = $scheduler->findFirstAvailableResource(
                    $tenantId,
                    $activeResources->pluck('id')->all(),
                    $when,
                    $duracao
                );
                if ($resolvedResourceId === null) {
                    $sugestoes = $scheduler->suggestNextSlots($tenantId, $training, $when, $duracao, 3);
                    return [
                        'ok' => false,
                        'erro' => 'nenhum_recurso_disponivel',
                        'mensagem' => "Nenhum recurso tem esse horario livre. Oferecer outro.",
                        'sugestoes' => $this->formatSlots($sugestoes),
                    ];
                }
            } else {
                $exists = $activeResources->firstWhere('id', $resourceIdInput);
                if (!$exists) {
                    return [
                        'ok' => false,
                        'erro' => 'recurso_invalido',
                        'mensagem' => "resource_id nao corresponde a nenhum recurso ativo. Chame listar_recursos pra obter IDs validos.",
                    ];
                }
                $resolvedResourceId = $resourceIdInput;
            }
        }

        $resolvedResource = $resolvedResourceId
            ? $activeResources->firstWhere('id', $resolvedResourceId)
            : null;

        if (!$scheduler->isWithinBusinessHours($when, $duracao, $training, $resolvedResource)) {
            $sugestoes = $scheduler->suggestNextSlots($tenantId, $training, $when, $duracao, 3, $resolvedResourceId, $resolvedResource);
            return [
                'ok' => false,
                'erro' => 'horario_fora_do_expediente',
                'mensagem' => "O horario {$when->format('d/m H:i')} esta fora do expediente.",
                'sugestoes' => $this->formatSlots($sugestoes),
            ];
        }

        $conflict = $scheduler->findConflict($tenantId, $when, $duracao, null, $resolvedResourceId);
        if ($conflict) {
            $sugestoes = $scheduler->suggestNextSlots($tenantId, $training, $when, $duracao, 3, $resolvedResourceId, $resolvedResource);
            return [
                'ok' => false,
                'erro' => 'conflito_de_horario',
                'mensagem' => "Ja existe um compromisso em {$conflict->scheduled_at->format('d/m H:i')} ({$conflict->duration_minutes}min). Oferecer outro horario.",
                'sugestoes' => $this->formatSlots($sugestoes),
            ];
        }

        $appointment = Appointment::create([
            'cliente_id' => $tenantId,
            'cliente_final_id' => $chat->cliente_final_id,
            'chat_id' => $chat->id,
            'resource_id' => $resolvedResourceId,
            'contato_nome' => $chat->contact_name,
            'contato_telefone' => $chat->contact_phone ?: $chat->wa_id,
            'titulo' => $titulo,
            'descricao' => $descricao,
            'scheduled_at' => $when,
            'duration_minutes' => $duracao,
            'status' => 'agendado',
            'created_via' => 'bot',
        ]);

        return [
            'ok' => true,
            'agendamento_id' => $appointment->id,
            'data_hora_confirmada' => $when->format('d/m/Y H:i'),
            'duracao_minutos' => $duracao,
            'titulo' => $titulo,
            'mensagem_para_cliente' => "Agendamento confirmado pra {$when->format('d/m/Y')} as {$when->format('H:i')}. Vou te mandar um lembrete antes.",
        ];
    }

    protected function formatSlots(array $slots): array
    {
        return array_map(fn ($s) => [
            'data_hora' => $s->format('Y-m-d H:i'),
            'legivel' => $s->isoFormat('ddd DD/MM HH:mm'),
        ], $slots);
    }
}
