<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentResource;
use App\Models\ClienteFinal;
use App\Models\WhatsappBotTraining;
use App\Services\CalendarTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $view = $request->query('view') === 'week' ? 'week' : 'list';

        if ($view === 'week') {
            return $this->weekView($request, $cliente);
        }

        $upcoming = Appointment::where('cliente_id', $cliente->id)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->orderBy('scheduled_at')
            ->with(['clienteFinal', 'resource'])
            ->get();

        $history = Appointment::where('cliente_id', $cliente->id)
            ->where(function ($q) {
                $q->whereIn('status', ['concluido', 'cancelado'])
                    ->orWhere('scheduled_at', '<', now()->startOfDay());
            })
            ->orderByDesc('scheduled_at')
            ->with(['clienteFinal', 'resource'])
            ->paginate(20);

        $icsUrl = $this->icsUrlForCliente($cliente);

        return view('customer.agenda.index', compact('upcoming', 'history', 'icsUrl'));
    }

    protected function icsUrlForCliente($cliente): string
    {
        $token = app(CalendarTokenService::class)->ensureToken($cliente);
        return route('public.calendar.cliente', ['token' => $token]);
    }

    protected function weekView(Request $request, $cliente)
    {
        $weekRef = $request->query('week');
        try {
            $weekStart = $weekRef ? Carbon::createFromFormat('Y-m-d', $weekRef) : now();
        } catch (\Throwable $e) {
            $weekStart = now();
        }
        $weekStart = $weekStart->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $appointments = Appointment::where('cliente_id', $cliente->id)
            ->whereBetween('scheduled_at', [$weekStart, $weekEnd])
            ->orderBy('scheduled_at')
            ->with(['clienteFinal', 'resource'])
            ->get()
            ->groupBy(fn (Appointment $a) => $a->scheduled_at->format('Y-m-d'));

        $training = WhatsappBotTraining::where('cliente_id', $cliente->id)->first();

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $weekStart->copy()->addDays($i);
            $days[] = [
                'date' => $d,
                'key' => $d->format('Y-m-d'),
                'label' => $d->isoFormat('ddd DD/MM'),
                'isToday' => $d->isSameDay(now()),
                'appointments' => $appointments->get($d->format('Y-m-d'), collect())->sortBy('scheduled_at'),
            ];
        }

        return view('customer.agenda.week', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'prevWeek' => $weekStart->copy()->subWeek()->format('Y-m-d'),
            'nextWeek' => $weekStart->copy()->addWeek()->format('Y-m-d'),
            'thisWeek' => now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
            'days' => $days,
            'training' => $training,
        ]);
    }

    public function create()
    {
        $cliente = Auth::user()->cliente;
        $clientesFinais = ClienteFinal::where('cliente_id', $cliente->id)
            ->orderBy('nome_responsavel')
            ->get();
        $resources = AppointmentResource::where('cliente_id', $cliente->id)->active()->ordered()->get();
        $resourceLabel = $this->resourceLabel($cliente->id);
        $appointment = new Appointment();

        return view('customer.agenda.form', compact('appointment', 'clientesFinais', 'resources', 'resourceLabel'));
    }

    public function store(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $data = $this->validatedPayload($request, $cliente->id);

        $data['cliente_id'] = $cliente->id;
        $data['created_via'] = 'manual';
        $data['status'] = $data['status'] ?? 'agendado';

        Appointment::create($data);

        return redirect()->route('customer.agenda.index')->with('success', 'Agendamento criado com sucesso.');
    }

    public function edit(Appointment $agenda)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $agenda->cliente_id === (int) $cliente->id, 404);

        $clientesFinais = ClienteFinal::where('cliente_id', $cliente->id)
            ->orderBy('nome_responsavel')
            ->get();
        $resources = AppointmentResource::where('cliente_id', $cliente->id)->active()->ordered()->get();
        $resourceLabel = $this->resourceLabel($cliente->id);

        return view('customer.agenda.form', [
            'appointment' => $agenda,
            'clientesFinais' => $clientesFinais,
            'resources' => $resources,
            'resourceLabel' => $resourceLabel,
        ]);
    }

    protected function resourceLabel(int $clienteId): string
    {
        return WhatsappBotTraining::where('cliente_id', $clienteId)->value('resource_label') ?: 'Profissional';
    }

    public function update(Request $request, Appointment $agenda)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $agenda->cliente_id === (int) $cliente->id, 404);

        $data = $this->validatedPayload($request, $cliente->id);
        $agenda->update($data);

        return redirect()->route('customer.agenda.index')->with('success', 'Agendamento atualizado.');
    }

    public function destroy(Appointment $agenda)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $agenda->cliente_id === (int) $cliente->id, 404);
        $agenda->delete();

        return redirect()->route('customer.agenda.index')->with('success', 'Agendamento excluído.');
    }

    public function markCompleted(Appointment $agenda)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $agenda->cliente_id === (int) $cliente->id, 404);
        $agenda->update(['status' => 'concluido']);

        return back()->with('success', 'Agendamento marcado como concluído.');
    }

    public function markCancelled(Appointment $agenda)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $agenda->cliente_id === (int) $cliente->id, 404);
        $agenda->update(['status' => 'cancelado', 'cancelled_at' => now()]);

        return back()->with('success', 'Agendamento cancelado.');
    }

    protected function validatedPayload(Request $request, int $clienteId): array
    {
        $v = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'descricao' => ['nullable', 'string', 'max:2000'],
            'cliente_final_id' => ['nullable', 'integer'],
            'resource_id' => ['nullable', 'integer'],
            'contato_nome' => ['nullable', 'string', 'max:160'],
            'contato_telefone' => ['nullable', 'string', 'max:30'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'status' => ['nullable', 'string', 'in:' . implode(',', Appointment::STATUSES)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (!empty($v['cliente_final_id'])) {
            $exists = ClienteFinal::where('id', $v['cliente_final_id'])
                ->where('cliente_id', $clienteId)
                ->exists();
            abort_unless($exists, 422, 'Cliente final inválido.');
        } else {
            $v['cliente_final_id'] = null;
        }

        if (!empty($v['resource_id'])) {
            $exists = AppointmentResource::where('id', $v['resource_id'])
                ->where('cliente_id', $clienteId)
                ->exists();
            abort_unless($exists, 422, 'Recurso inválido.');
        } else {
            $v['resource_id'] = null;
        }

        $v['scheduled_at'] = Carbon::createFromFormat('Y-m-d H:i', $v['scheduled_date'] . ' ' . $v['scheduled_time']);
        unset($v['scheduled_date'], $v['scheduled_time']);

        return $v;
    }
}
