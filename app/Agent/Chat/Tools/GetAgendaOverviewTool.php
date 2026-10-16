<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Agent\Metrics\AppointmentsMetric;
use App\Models\Appointment;
use Carbon\Carbon;
use Throwable;

class GetAgendaOverviewTool implements ToolInterface
{
    public function __construct(
        private AppointmentsMetric $metric
    ) {
    }

    public function getName(): string
    {
        return 'get_agenda_overview';
    }

    public function getDescription(): string
    {
        return 'Retorna o panorama da Agenda do negócio (resumo numérico + lista detalhada de compromissos '
             . 'com horário, duração, status, cliente/contato, profissional/recurso alocado e se foi criado pelo bot). '
             . 'USE ESTA TOOL quando o gestor perguntar sobre a agenda dele: "o que tenho hoje?", '
             . '"quais os agendamentos da semana?", "quem agendou pelo WhatsApp?", "tem compromisso pendente?".';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'data_inicio' => [
                    'type' => 'string',
                    'description' => 'Data inicial opcional no formato YYYY-MM-DD (padrão: hoje).',
                ],
                'data_fim' => [
                    'type' => 'string',
                    'description' => 'Data final opcional no formato YYYY-MM-DD (padrão: hoje + 7 dias).',
                ],
                'status' => [
                    'type' => 'string',
                    'description' => 'Filtro opcional de status: agendado, confirmado, concluido, cancelado ou todos (padrão: ativos).',
                ],
            ],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        try {
            $start = !empty($arguments['data_inicio'])
                ? Carbon::createFromFormat('Y-m-d', (string) $arguments['data_inicio'])->startOfDay()
                : Carbon::now()->startOfDay();
            $end = !empty($arguments['data_fim'])
                ? Carbon::createFromFormat('Y-m-d', (string) $arguments['data_fim'])->endOfDay()
                : Carbon::now()->addDays(7)->endOfDay();
        } catch (Throwable) {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->addDays(7)->endOfDay();
        }

        $statusFilter = $arguments['status'] ?? 'ativos';

        $query = Appointment::with(['clienteFinal', 'resource'])
            ->where('cliente_id', $tenantId)
            ->whereBetween('scheduled_at', [$start, $end]);

        if (in_array($statusFilter, Appointment::STATUSES, true)) {
            $query->where('status', $statusFilter);
        } elseif ($statusFilter !== 'todos') {
            $query->whereIn('status', ['agendado', 'confirmado']);
        }

        $appointments = $query
            ->orderBy('scheduled_at')
            ->limit(30)
            ->get();

        $lista = $appointments->map(function (Appointment $a) {
            return [
                'id' => (int) $a->id,
                'data_hora' => $a->scheduled_at->format('d/m/Y H:i'),
                'duracao_minutos' => (int) $a->duration_minutes,
                'titulo' => $a->titulo,
                'status' => $a->status,
                'cliente_nome' => $a->contato_nome ?: ($a->clienteFinal?->nome_responsavel ?? $a->clienteFinal?->nome_empresa ?? 'Sem nome'),
                'telefone' => $a->contato_telefone ?: ($a->clienteFinal?->telefone ?? null),
                'recurso' => $a->resource?->name,
                'origem' => $a->created_via,
                'lembrete_enviado' => $a->reminder_sent_at !== null,
            ];
        })->values()->all();

        return [
            'janela_consultada' => $start->format('d/m/Y') . ' a ' . $end->format('d/m/Y'),
            'resumo_30d' => $this->metric->calculate($tenantId, 30),
            'total_listados' => count($lista),
            'compromissos' => $lista,
        ];
    }
}
