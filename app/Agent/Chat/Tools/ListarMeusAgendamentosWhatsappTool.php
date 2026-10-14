<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\Appointment;
use App\Models\WhatsappChat;

class ListarMeusAgendamentosWhatsappTool implements ToolInterface
{
    public function getName(): string
    {
        return 'listar_meus_agendamentos';
    }

    public function getDescription(): string
    {
        return 'Lista os agendamentos ativos (agendado ou confirmado) do CLIENTE QUE ESTA CONVERSANDO. '
            . 'USE quando o cliente pedir pra cancelar, remarcar, confirmar ou saber "qual meu proximo horario". '
            . 'Retorna id, data_hora, titulo. Se vier vazio, informe ao cliente que nao ha nada agendado em nome dele.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => new \stdClass(),
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $chatId = (int) ($arguments['_chat_id'] ?? 0);
        $chat = WhatsappChat::where('id', $chatId)->where('cliente_id', $tenantId)->first();
        if (!$chat) {
            return ['erro' => 'contexto do chat ausente.'];
        }

        $query = Appointment::where('cliente_id', $tenantId)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->orderBy('scheduled_at');

        if ($chat->cliente_final_id) {
            $query->where('cliente_final_id', $chat->cliente_final_id);
        } else {
            $query->where('chat_id', $chat->id);
        }

        $items = $query->limit(10)->get()->map(fn (Appointment $a) => [
            'id' => $a->id,
            'data_hora' => $a->scheduled_at->format('Y-m-d H:i'),
            'legivel' => $a->scheduled_at->isoFormat('ddd DD/MM HH:mm'),
            'duracao_minutos' => $a->duration_minutes,
            'titulo' => $a->titulo,
            'status' => $a->status,
        ]);

        return [
            'total' => $items->count(),
            'agendamentos' => $items->values()->all(),
        ];
    }
}
