<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\PedidoCliente;
use App\Models\WhatsappBotInteraction;
use App\Models\WhatsappChat;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WhatsappDashboardController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;

        $range = $request->query('range') === '30d' ? 30 : 7;
        $start = now()->subDays($range - 1)->startOfDay();
        $end = now()->endOfDay();

        $inboundCount = WhatsappMessage::where('cliente_id', $cliente->id)
            ->where('direction', 'inbound')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $interactions = WhatsappBotInteraction::where('cliente_id', $cliente->id)
            ->whereBetween('created_at', [$start, $end]);

        $totalInteractions = (clone $interactions)->count();
        $successfulReplies = (clone $interactions)->where('escalated', false)->where('failed', false)->count();
        $escalations = (clone $interactions)->where('escalated', true)->count();
        $failures = (clone $interactions)->where('failed', true)->count();

        $avgResponseMs = (int) (clone $interactions)->where('failed', false)->avg('response_time_ms');

        $responseRate = $inboundCount > 0 ? round(($successfulReplies / $inboundCount) * 100, 1) : 0;

        $uniqueChatIds = (clone $interactions)->distinct()->pluck('chat_id');
        $uniqueCustomers = $uniqueChatIds->count();

        $returningCustomers = $uniqueChatIds->isNotEmpty()
            ? WhatsappChat::whereIn('id', $uniqueChatIds)
                ->whereHas('messages', function ($q) use ($start) {
                    $q->where('direction', 'outbound')->where('created_at', '<', $start);
                })
                ->count()
            : 0;

        $clienteFinalIds = $uniqueChatIds->isNotEmpty()
            ? WhatsappChat::whereIn('id', $uniqueChatIds)->whereNotNull('cliente_final_id')->pluck('cliente_final_id')->unique()
            : collect();

        $pedidosGerados = $clienteFinalIds->isNotEmpty()
            ? PedidoCliente::where('cliente_id', $cliente->id)
                ->whereIn('cliente_final_id', $clienteFinalIds)
                ->whereBetween('created_at', [$start, $end])
                ->count()
            : 0;

        $daily = $this->buildDailySeries($cliente->id, $start, $end, $range);
        $agenda = $this->buildAgendaMetrics($cliente->id, $start, $end);

        return view('customer.whatsapp.dashboard', [
            'range' => $range,
            'inboundCount' => $inboundCount,
            'successfulReplies' => $successfulReplies,
            'escalations' => $escalations,
            'failures' => $failures,
            'totalInteractions' => $totalInteractions,
            'responseRate' => $responseRate,
            'avgResponseMs' => $avgResponseMs,
            'uniqueCustomers' => $uniqueCustomers,
            'returningCustomers' => $returningCustomers,
            'pedidosGerados' => $pedidosGerados,
            'dailyLabels' => $daily['labels'],
            'dailyInbound' => $daily['inbound'],
            'dailyReplies' => $daily['replies'],
            'agenda' => $agenda,
        ]);
    }

    protected function buildAgendaMetrics(int $clienteId, Carbon $start, Carbon $end): array
    {
        $createdTotal = Appointment::where('cliente_id', $clienteId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $createdByBot = Appointment::where('cliente_id', $clienteId)
            ->where('created_via', 'bot')
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $cancelled = Appointment::where('cliente_id', $clienteId)
            ->whereBetween('cancelled_at', [$start, $end])
            ->count();

        $errorsByCode = WhatsappBotInteraction::where('cliente_id', $clienteId)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('tool_errors')
            ->get(['tool_errors'])
            ->flatMap(fn ($i) => collect($i->tool_errors ?? [])->pluck('error'))
            ->countBy()
            ->toArray();

        // Conversao: chats unicos no periodo vs chats que viraram appointment
        $chatsUnicos = WhatsappBotInteraction::where('cliente_id', $clienteId)
            ->whereBetween('created_at', [$start, $end])
            ->distinct('chat_id')
            ->count('chat_id');
        $chatsComAppointment = Appointment::where('cliente_id', $clienteId)
            ->where('created_via', 'bot')
            ->whereNotNull('chat_id')
            ->whereBetween('created_at', [$start, $end])
            ->distinct('chat_id')
            ->count('chat_id');
        $conversionRate = $chatsUnicos > 0 ? round(($chatsComAppointment / $chatsUnicos) * 100, 1) : 0;

        // No-show: appointments passados que ficaram status=agendado/confirmado
        $pastQuery = Appointment::where('cliente_id', $clienteId)
            ->whereBetween('scheduled_at', [$start, $end])
            ->where('scheduled_at', '<', now());
        $pastTotal = (clone $pastQuery)->whereIn('status', ['agendado', 'confirmado', 'concluido'])->count();
        $pastNoShow = (clone $pastQuery)->whereIn('status', ['agendado', 'confirmado'])->count();
        $noShowRate = $pastTotal > 0 ? round(($pastNoShow / $pastTotal) * 100, 1) : 0;

        // Melhor hora e dia da semana (quando mais sao criados)
        $createdWithTime = Appointment::where('cliente_id', $clienteId)
            ->whereBetween('created_at', [$start, $end])
            ->get(['scheduled_at']);
        $bestHour = null;
        $bestDow = null;
        if ($createdWithTime->isNotEmpty()) {
            $byHour = $createdWithTime->groupBy(fn ($a) => $a->scheduled_at->format('H'))
                ->map->count()->sortDesc();
            $bestHour = (int) $byHour->keys()->first();

            $dowNames = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sab'];
            $byDow = $createdWithTime->groupBy(fn ($a) => $a->scheduled_at->dayOfWeek)
                ->map->count()->sortDesc();
            $bestDowIdx = (int) $byDow->keys()->first();
            $bestDow = ['dom', 'segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado'][$bestDowIdx] ?? null;
        }

        return [
            'created_total' => $createdTotal,
            'created_by_bot' => $createdByBot,
            'cancelled' => $cancelled,
            'conflito_count' => (int) ($errorsByCode['conflito_de_horario'] ?? 0),
            'fora_expediente_count' => (int) ($errorsByCode['horario_fora_do_expediente'] ?? 0),
            'conversion_rate' => $conversionRate,
            'chats_unicos' => $chatsUnicos,
            'chats_com_appointment' => $chatsComAppointment,
            'no_show_rate' => $noShowRate,
            'past_total' => $pastTotal,
            'past_no_show' => $pastNoShow,
            'best_hour' => $bestHour,
            'best_dow' => $bestDow,
        ];
    }

    protected function buildDailySeries(int $clienteId, Carbon $start, Carbon $end, int $range): array
    {
        $inboundByDay = WhatsappMessage::where('cliente_id', $clienteId)
            ->where('direction', 'inbound')
            ->whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')
            ->pluck('total', 'd');

        $repliesByDay = WhatsappBotInteraction::where('cliente_id', $clienteId)
            ->where('escalated', false)
            ->where('failed', false)
            ->whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')
            ->pluck('total', 'd');

        $labels = [];
        $inbound = [];
        $replies = [];
        for ($i = 0; $i < $range; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d/m');
            $inbound[] = (int) ($inboundByDay[$key] ?? 0);
            $replies[] = (int) ($repliesByDay[$key] ?? 0);
        }

        return compact('labels', 'inbound', 'replies');
    }
}
