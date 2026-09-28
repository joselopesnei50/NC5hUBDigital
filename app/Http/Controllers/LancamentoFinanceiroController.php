<?php

namespace App\Http\Controllers;

use App\Models\LancamentoFinanceiro;
use App\Models\ClienteFinal;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LancamentoFinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->get('tipo', 'receber');
        $lancamentos = LancamentoFinanceiro::with('clienteFinal')
            ->where('cliente_id', Auth::user()->cliente->id)
            ->where('tipo', $tipo)
            ->orderBy('data_vencimento', 'asc')
            ->get();

        return view('customer.lancamentos.index', compact('lancamentos', 'tipo'));
    }

    public function create()
    {
        $clienteId = Auth::user()->cliente->id;
        $clientesFinais = ClienteFinal::where('cliente_id', $clienteId)->get();
        $fornecedores  = Fornecedor::where('cliente_id', $clienteId)->orderBy('nome')->get();
        return view('customer.lancamentos.create', compact('clientesFinais', 'fornecedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:receber,pagar',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'cliente_final_id' => 'nullable|exists:clientes_finais,id',
        ]);

        $data = $request->all();
        $data['cliente_id'] = Auth::user()->cliente->id; // Tenant isolation
        
        LancamentoFinanceiro::create($data);

        return redirect()->route('customer.lancamentos.index', ['tipo' => $request->tipo])
            ->with('success', 'Lançamento criado com sucesso.');
    }

    public function edit(LancamentoFinanceiro $lancamento)
    {
        if ($lancamento->cliente_id !== Auth::user()->cliente->id) abort(403);

        $clienteId = Auth::user()->cliente->id;
        $clientesFinais = ClienteFinal::where('cliente_id', $clienteId)->get();
        $fornecedores  = Fornecedor::where('cliente_id', $clienteId)->orderBy('nome')->get();
        return view('customer.lancamentos.edit', compact('lancamento', 'clientesFinais', 'fornecedores'));
    }

    public function update(Request $request, LancamentoFinanceiro $lancamento)
    {
        if ($lancamento->cliente_id !== Auth::user()->cliente->id) abort(403);

        $request->validate([
            'tipo' => 'required|in:receber,pagar',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'cliente_final_id' => 'nullable|exists:clientes_finais,id',
        ]);

        $lancamento->update($request->all());

        return redirect()->route('customer.lancamentos.index', ['tipo' => $lancamento->tipo])
            ->with('success', 'Lançamento atualizado com sucesso.');
    }

    public function destroy(LancamentoFinanceiro $lancamento)
    {
        if ($lancamento->cliente_id !== Auth::user()->cliente->id) abort(403);

        $tipo = $lancamento->tipo;
        $lancamento->delete();

        return redirect()->route('customer.lancamentos.index', ['tipo' => $tipo])
            ->with('success', 'Lançamento excluído com sucesso.');
    }
}
