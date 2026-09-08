<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardVendasController extends Controller
{
    public function index()
    {
        $cliente = Auth::user()->cliente;

        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;

        // Status considerados como "Venda Fechada"
        $statusVendas = ['Aguardando Pagamento', 'Em Andamento', 'Concluído'];

        // Faturamento do Mês
        $faturamentoMes = $cliente->pedidosClientesFinais()
            ->whereIn('status', $statusVendas)
            ->whereMonth('created_at', $mesAtual)
            ->whereYear('created_at', $anoAtual)
            ->sum('valor');

        // Pedidos Aprovados no Mês
        $pedidosAprovados = $cliente->pedidosClientesFinais()
            ->whereIn('status', $statusVendas)
            ->whereMonth('created_at', $mesAtual)
            ->whereYear('created_at', $anoAtual)
            ->count();

        // Novos Clientes no Mês
        $novosClientes = $cliente->clientesFinais()
            ->whereMonth('created_at', $mesAtual)
            ->whereYear('created_at', $anoAtual)
            ->count();

        // Últimas Vendas
        $ultimasVendas = $cliente->pedidosClientesFinais()
            ->with('clienteFinal')
            ->whereIn('status', $statusVendas)
            ->latest()
            ->take(5)
            ->get();

        // Gráfico de Faturamento (Últimos 6 Meses)
        $graficoLabels = [];
        $graficoValores = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            
            $faturamento = $cliente->pedidosClientesFinais()
                ->whereIn('status', $statusVendas)
                ->whereMonth('created_at', $mes->month)
                ->whereYear('created_at', $mes->year)
                ->sum('valor');

            $graficoLabels[] = $mes->translatedFormat('M/Y'); // Ex: Set/2026
            $graficoValores[] = $faturamento;
        }

        return view('customer.dashboard_vendas.index', compact(
            'faturamentoMes',
            'pedidosAprovados',
            'novosClientes',
            'ultimasVendas',
            'graficoLabels',
            'graficoValores'
        ));
    }
}
