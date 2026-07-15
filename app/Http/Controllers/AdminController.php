<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Fatura;
use App\Models\Ticket;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $clientesAtivos = Cliente::where('status', 'ativo')->count();
        $contratosVigentes = Contrato::where('status', 'ativo')->count();

        $inicioMes = Carbon::now()->startOfMonth();
        $faturamentoMes = Fatura::where('status', 'pago')
            ->where('updated_at', '>=', $inicioMes)
            ->sum('valor');
        $faturasPendentes = Fatura::where('status', 'pendente')->count();

        $ticketsAbertos = Ticket::where('status', 'aberto')->count();

        $ultimosClientes = Cliente::with('user')->latest()->take(5)->get();
        $ultimosLeads = Lead::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'clientesAtivos',
            'contratosVigentes',
            'faturamentoMes',
            'faturasPendentes',
            'ticketsAbertos',
            'ultimosClientes',
            'ultimosLeads'
        ));
    }
}
