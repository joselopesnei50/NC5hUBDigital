<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClienteFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;

        $upcoming = Appointment::where('cliente_id', $cliente->id)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->orderBy('scheduled_at')
            ->with('clienteFinal')
            ->get();

        $history = Appointment::where('cliente_id', $cliente->id)
            ->where(function ($q) {
                $q->whereIn('status', ['concluido', 'cancelado'])
                    ->orWhere('scheduled_at', '<', now()->startOfDay());
            })
            ->orderByDesc('scheduled_at')
            ->with('clienteFinal')
            ->paginate(20);

        return view('customer.agenda.index', compact('upcoming', 'history'));
    }

    public function create()
    {
        $cliente = Auth::user()->cliente;
        $clientesFinais = ClienteFinal::where('cliente_id', $cliente->id)
            ->orderBy('nome_responsavel')
            ->get();
        $appointment = new Appointment();

        return view('customer.agenda.form', compact('appointment', 'clientesFinais'));
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

        return view('customer.agenda.form', ['appointment' => $agenda, 'clientesFinais' => $clientesFinais]);
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
        $agenda->update(['status' => 'cancelado']);

        return back()->with('success', 'Agendamento cancelado.');
    }

    protected function validatedPayload(Request $request, int $clienteId): array
    {
        $v = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'descricao' => ['nullable', 'string', 'max:2000'],
            'cliente_final_id' => ['nullable', 'integer'],
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

        $v['scheduled_at'] = Carbon::createFromFormat('Y-m-d H:i', $v['scheduled_date'] . ' ' . $v['scheduled_time']);
        unset($v['scheduled_date'], $v['scheduled_time']);

        return $v;
    }
}
