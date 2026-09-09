<?php

namespace App\Http\Controllers;

use App\Models\LancamentoFinanceiro;
use App\Models\Cliente;
use Illuminate\Http\Request;

class LancamentoFinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->get('tipo', 'receber');
        $lancamentos = LancamentoFinanceiro::with('cliente')
            ->where('tipo', $tipo)
            ->orderBy('data_vencimento', 'asc')
            ->get();

        return view('customer.lancamentos.index', compact('lancamentos', 'tipo'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('customer.lancamentos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:receber,pagar',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date',
        ]);

        LancamentoFinanceiro::create($request->all());

        return redirect()->route('customer.lancamentos.index', ['tipo' => $request->tipo])
            ->with('success', 'Lançamento criado com sucesso.');
    }

    public function edit(LancamentoFinanceiro $lancamento)
    {
        $clientes = Cliente::all();
        return view('customer.lancamentos.edit', compact('lancamento', 'clientes'));
    }

    public function update(Request $request, LancamentoFinanceiro $lancamento)
    {
        $request->validate([
            'tipo' => 'required|in:receber,pagar',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date',
        ]);

        $lancamento->update($request->all());

        return redirect()->route('customer.lancamentos.index', ['tipo' => $lancamento->tipo])
            ->with('success', 'Lançamento atualizado com sucesso.');
    }

    public function destroy(LancamentoFinanceiro $lancamento)
    {
        $tipo = $lancamento->tipo;
        $lancamento->delete();

        return redirect()->route('customer.lancamentos.index', ['tipo' => $tipo])
            ->with('success', 'Lançamento excluído com sucesso.');
    }
}
