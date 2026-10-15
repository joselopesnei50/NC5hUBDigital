<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AppointmentResource;
use App\Models\WhatsappBotTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentResourceController extends Controller
{
    public function index()
    {
        $cliente = Auth::user()->cliente;
        $resources = AppointmentResource::where('cliente_id', $cliente->id)
            ->ordered()
            ->get();
        $label = $this->label($cliente->id);

        return view('customer.agenda.recursos.index', compact('resources', 'label'));
    }

    public function create()
    {
        $cliente = Auth::user()->cliente;
        $resource = new AppointmentResource(['is_active' => true, 'ordem' => 0]);
        $label = $this->label($cliente->id);

        return view('customer.agenda.recursos.form', compact('resource', 'label'));
    }

    public function store(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $data = $this->validated($request);
        $data['cliente_id'] = $cliente->id;
        AppointmentResource::create($data);

        return redirect()->route('customer.agenda.recursos.index')->with('success', 'Cadastrado com sucesso.');
    }

    public function edit(AppointmentResource $recurso)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $recurso->cliente_id === (int) $cliente->id, 404);
        $label = $this->label($cliente->id);

        return view('customer.agenda.recursos.form', ['resource' => $recurso, 'label' => $label]);
    }

    public function update(Request $request, AppointmentResource $recurso)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $recurso->cliente_id === (int) $cliente->id, 404);
        $recurso->update($this->validated($request));

        return redirect()->route('customer.agenda.recursos.index')->with('success', 'Atualizado.');
    }

    public function destroy(AppointmentResource $recurso)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $recurso->cliente_id === (int) $cliente->id, 404);
        $recurso->delete();

        return redirect()->route('customer.agenda.recursos.index')->with('success', 'Excluído.');
    }

    protected function validated(Request $request): array
    {
        $v = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'ordem' => ['nullable', 'integer', 'min:0', 'max:999'],
            'use_custom_hours' => ['nullable', 'boolean'],
            'horario_comercial' => ['nullable', 'array'],
            'horario_comercial.*.aberto' => ['nullable', 'boolean'],
            'horario_comercial.*.abre' => ['nullable', 'date_format:H:i'],
            'horario_comercial.*.fecha' => ['nullable', 'date_format:H:i'],
        ]);

        $useCustom = (bool) $request->input('use_custom_hours', false);
        $v['use_custom_hours'] = $useCustom;
        $v['is_active'] = (bool) $request->input('is_active', false);

        if ($useCustom) {
            $horario = [];
            foreach (\App\Models\WhatsappBotTraining::DIAS_SEMANA as $dia) {
                $entry = $v['horario_comercial'][$dia] ?? [];
                $horario[$dia] = [
                    'aberto' => (bool) ($entry['aberto'] ?? false),
                    'abre' => $entry['abre'] ?? '09:00',
                    'fecha' => $entry['fecha'] ?? '18:00',
                ];
            }
            $v['horario_comercial'] = $horario;
        } else {
            $v['horario_comercial'] = null;
        }

        return $v;
    }

    protected function label(int $clienteId): string
    {
        return WhatsappBotTraining::where('cliente_id', $clienteId)->value('resource_label') ?: 'Profissional';
    }
}
