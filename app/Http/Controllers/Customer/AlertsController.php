<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AgentAlert;
use Illuminate\Support\Facades\Auth;

class AlertsController extends Controller
{
    protected function requireCliente()
    {
        $cliente = Auth::user()->cliente ?? null;
        if (!$cliente) {
            abort(403);
        }
        return $cliente;
    }

    public function index()
    {
        $cliente = $this->requireCliente();

        $ativos = AgentAlert::query()
            ->doCliente($cliente->id)
            ->ativos()
            ->latest()
            ->get();

        $arquivados = AgentAlert::query()
            ->doCliente($cliente->id)
            ->whereNotNull('dispensado_em')
            ->latest('dispensado_em')
            ->limit(30)
            ->get();

        return view('customer.alertas.index', compact('ativos', 'arquivados'));
    }

    public function marcarLido(AgentAlert $alerta)
    {
        $cliente = $this->requireCliente();
        if ((int) $alerta->cliente_id !== (int) $cliente->id) {
            abort(403);
        }

        if (!$alerta->lido_em) {
            $alerta->update(['lido_em' => now()]);
        }

        return back();
    }

    public function dispensar(AgentAlert $alerta)
    {
        $cliente = $this->requireCliente();
        if ((int) $alerta->cliente_id !== (int) $cliente->id) {
            abort(403);
        }

        $alerta->update(['dispensado_em' => now()]);

        return back()->with('success', 'Alerta dispensado. Se o problema persistir, o Bruce reemite na próxima análise.');
    }
}
