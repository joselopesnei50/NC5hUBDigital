<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\Appointment;
use App\Models\WhatsappChat;

class CancelarAgendamentoWhatsappTool implements ToolInterface
{
    public function getName(): string
    {
        return 'cancelar_agendamento';
    }

    public function getDescription(): string
    {
        return 'Cancela um agendamento identificado por appointment_id. '
            . 'USE SOMENTE depois que: (1) voce ja listou os agendamentos do cliente com listar_meus_agendamentos; '
            . '(2) voce CONFIRMOU explicitamente qual (data+titulo) ele quer cancelar; (3) o cliente respondeu SIM. '
            . 'Nao chame essa tool por iniciativa propria. '
            . 'Se o cliente quiser remarcar, cancele primeiro e depois chame criar_agendamento no horario novo.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'appointment_id' => [
                    'type' => 'integer',
                    'description' => 'ID do agendamento retornado por listar_meus_agendamentos.',
                ],
                'motivo' => [
                    'type' => 'string',
                    'description' => 'Motivo opcional do cancelamento (ex: "cliente nao pode nesse horario").',
                ],
            ],
            'required' => ['appointment_id'],
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
        if ($appointmentId < 1) {
            return ['ok' => false, 'erro' => 'appointment_id invalido.'];
        }

        $apt = Appointment::where('id', $appointmentId)
            ->where('cliente_id', $tenantId)
            ->first();

        if (!$apt) {
            return ['ok' => false, 'erro' => 'agendamento nao encontrado nessa base.'];
        }

        $belongsToThisChat = $chat->cliente_final_id
            ? ((int) $apt->cliente_final_id === (int) $chat->cliente_final_id)
            : ((int) $apt->chat_id === (int) $chat->id);

        if (!$belongsToThisChat) {
            return ['ok' => false, 'erro' => 'esse agendamento nao e desse contato.'];
        }

        if (!$apt->isActive()) {
            return ['ok' => false, 'erro' => "agendamento ja esta {$apt->status}."];
        }

        $motivo = trim((string) ($arguments['motivo'] ?? ''));
        $notes = trim(($apt->notes ?: '') . "\n[cancelado via bot] " . ($motivo ?: 'sem motivo informado'));

        $apt->update([
            'status' => 'cancelado',
            'cancelled_at' => now(),
            'notes' => $notes,
        ]);

        return [
            'ok' => true,
            'cancelado_id' => $apt->id,
            'data_hora_cancelada' => $apt->scheduled_at->format('d/m/Y H:i'),
            'mensagem_para_cliente' => "Pronto, cancelei o agendamento de {$apt->scheduled_at->format('d/m H:i')}. Se quiser remarcar, so me avisar.",
        ];
    }
}
