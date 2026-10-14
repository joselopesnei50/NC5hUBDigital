<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\Appointment;
use Illuminate\Support\Carbon;

class ConsultarAgendaWhatsappTool implements ToolInterface
{
    public function getName(): string
    {
        return 'consultar_agenda';
    }

    public function getDescription(): string
    {
        return 'Consulta os agendamentos existentes do negocio em uma janela de datas. '
            . 'USE antes de propor um horario pra verificar se ja ha compromisso naquele dia/hora. '
            . 'Retorna lista com data_hora, duracao, titulo e status. '
            . 'Argumentos opcionais: data_inicio e data_fim (YYYY-MM-DD). Default: proximos 7 dias.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'data_inicio' => [
                    'type' => 'string',
                    'description' => 'Data inicial no formato YYYY-MM-DD. Default: hoje.',
                ],
                'data_fim' => [
                    'type' => 'string',
                    'description' => 'Data final no formato YYYY-MM-DD. Default: hoje + 7 dias.',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        try {
            $start = isset($arguments['data_inicio'])
                ? Carbon::createFromFormat('Y-m-d', $arguments['data_inicio'])->startOfDay()
                : now()->startOfDay();
            $end = isset($arguments['data_fim'])
                ? Carbon::createFromFormat('Y-m-d', $arguments['data_fim'])->endOfDay()
                : now()->addDays(7)->endOfDay();
        } catch (\Throwable $e) {
            return ['erro' => 'data_inicio ou data_fim invalida. Use YYYY-MM-DD.'];
        }

        $appointments = Appointment::where('cliente_id', $tenantId)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->whereBetween('scheduled_at', [$start, $end])
            ->orderBy('scheduled_at')
            ->limit(50)
            ->get();

        $items = $appointments->map(function (Appointment $a) {
            return [
                'id' => $a->id,
                'data_hora' => $a->scheduled_at->format('Y-m-d H:i'),
                'duracao_minutos' => $a->duration_minutes,
                'titulo' => $a->titulo,
                'status' => $a->status,
                'contato' => $a->contato_nome ?: ($a->clienteFinal?->nome_responsavel ?? null),
            ];
        });

        return [
            'periodo' => $start->format('d/m/Y') . ' a ' . $end->format('d/m/Y'),
            'total' => $items->count(),
            'agendamentos' => $items->values()->all(),
        ];
    }
}
