<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contato;
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
        $contatosNovos = Contato::where('status', 'novo')->count();

        $ultimosClientes = Cliente::with('user')->latest()->take(5)->get();
        $ultimosLeads = Lead::latest()->take(10)->get(); // Aumentei para 10 para ficar mais completo
        
        $leadsNoMes = Lead::where('created_at', '>=', $inicioMes)->count();

        return view('admin.dashboard', compact(
            'clientesAtivos',
            'contratosVigentes',
            'faturamentoMes',
            'faturasPendentes',
            'ticketsAbertos',
            'contatosNovos',
            'ultimosClientes',
            'ultimosLeads',
            'leadsNoMes'
        ));
    }
}
