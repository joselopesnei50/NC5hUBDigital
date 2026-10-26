<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\Appointment;
use App\Models\WhatsappChat;
use App\Services\AppointmentNotificationService;

class ConfirmarAgendamentoWhatsappTool implements ToolInterface
{
    public function getName(): string
    {
        return 'confirmar_agendamento';
    }

    public function getDescription(): string
    {
        return 'Confirma a presenca do cliente em um agendamento futuro (muda o status de agendado para confirmado). '
            . 'USE quando o cliente responder confirmando presenca (ex: "Sim", "Confirmo", "1", "Estarei la", "Confirmado") '
            . 'apos receber um lembrete de agendamento ou ao verificar seus horarios. '
            . 'Se appointment_id for omitido ou 0, confirma automaticamente o proximo agendamento futuro ativo deste contato.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'appointment_id' => [
                    'type' => 'integer',
                    'description' => 'ID do agendamento a confirmar (opcional se o cliente tiver apenas um proximo agendamento ativo).',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $chatId = (int) ($arguments['_chat_id'] ?? 0);
        $chat = WhatsappChat::where('id', $chatId)->where('cliente_id', $tenantId)->first();
        if (!$chat) {
            return ['ok' => false, 'erro' => 'contexto do chat ausente.'];
        }

        $appointmentId = (int) ($arguments['appointment_id'] ?? 0);

        $query = Appointment::where('cliente_id', $tenantId)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->where('scheduled_at', '>=', now()->subHours(2))
            ->where(function ($q) use ($chat) {
                $q->where('chat_id', $chat->id);
                if ($chat->cliente_final_id) {
                    $q->orWhere('cliente_final_id', $chat->cliente_final_id);
                }
            })
            ->orderBy('scheduled_at');

        if ($appointmentId > 0) {
            $apt = (clone $query)->where('id', $appointmentId)->first() ?: $query->first();
        } else {
            $apt = $query->first();
        }

        if (!$apt) {
            return [
                'ok' => false,
                'erro' => 'nenhum_agendamento_futuro_encontrado',
                'mensagem' => 'Nao encontrei nenhum agendamento futuro ativo para este contato.',
            ];
        }

        $alreadyConfirmed = $apt->status === 'confirmado';

        $apt->update([
            'status' => 'confirmado',
            'confirmed_at' => $apt->confirmed_at ?: now(),
        ]);

        if (!$alreadyConfirmed) {
            app(AppointmentNotificationService::class)->notifyConfirmed($apt);
        }

        return [
            'ok' => true,
            'confirmado_id' => $apt->id,
            'titulo' => $apt->titulo,
            'data_hora' => $apt->scheduled_at->format('d/m/Y H:i'),
            'status' => 'confirmado',
            'mensagem_para_cliente' => "Perfeito! Sua presenca para {$apt->titulo} em {$apt->scheduled_at->format('d/m/Y')} as {$apt->scheduled_at->format('H:i')} esta confirmada. Ate la!",
        ];
    }
}
