<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProdutoServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdutoServicoController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $search = $request->input('search');

        $query = $cliente->produtosServicos();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        $produtos = $query->orderBy('tipo')->orderBy('nome')->paginate(15);

        return view('customer.produtos_servicos.index', compact('produtos', 'search'));
    }

    public function create()
    {
        return view('customer.produtos_servicos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|in:Produto,Serviço',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco_padrao' => 'required|numeric|min:0',
        ]);

        $cliente = Auth::user()->cliente;
        $cliente->produtosServicos()->create($validated);

        return redirect()->route('customer.produtos.index')->with('success', 'Cadastrado com sucesso!');
    }

    public function edit(ProdutoServico $produto)
    {
        $cliente = Auth::user()->cliente;
        
        if ($produto->cliente_id !== $cliente->id) {
            abort(403);
        }

        return view('customer.produtos_servicos.edit', compact('produto'));
    }

    public function update(Request $request, ProdutoServico $produto)
    {
        $cliente = Auth::user()->cliente;
        
        if ($produto->cliente_id !== $cliente->id) {
            abort(403);
        }

        $validated = $request->validate([
            'tipo' => 'required|string|in:Produto,Serviço',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco_padrao' => 'required|numeric|min:0',
        ]);

        $produto->update($validated);

        return redirect()->route('customer.produtos.index')->with('success', 'Atualizado com sucesso!');
    }

    public function destroy(ProdutoServico $produto)
    {
        $cliente = Auth::user()->cliente;
        
        if ($produto->cliente_id !== $cliente->id) {
            abort(403);
        }

        $produto->delete();

        return redirect()->route('customer.produtos.index')->with('success', 'Excluído com sucesso!');
    }
}
