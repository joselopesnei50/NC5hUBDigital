<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ClienteFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteFinalController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $search = $request->input('search');

        $query = $cliente->clientesFinais();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nome_empresa', 'like', "%{$search}%")
                  ->orWhere('nome_responsavel', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%");
            });
        }

        $clientesFinais = $query->latest()->paginate(10);

        return view('customer.clientes_finais.index', compact('clientesFinais', 'search'));
    }

    public function create()
    {
        return view('customer.clientes_finais.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_empresa' => 'required|string|max:255',
            'nome_responsavel' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cidade' => 'nullable|string|max:255',
            'ultimos_pedidos' => 'nullable|string',
            'data_aniversario_empresa' => 'nullable|date',
            'data_aniversario_responsavel' => 'nullable|date',
        ]);

        $cliente = Auth::user()->cliente;
        $cliente->clientesFinais()->create($validated);

        return redirect()->route('customer.clientes-finais.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit(ClienteFinal $clientes_finai)
    {
        $cliente = Auth::user()->cliente;
        
        // Verifica se o cliente final pertence a este cliente
        if ($clientes_finai->cliente_id !== $cliente->id) {
            abort(403);
        }

        return view('customer.clientes_finais.edit', ['clienteFinal' => $clientes_finai]);
    }

    public function update(Request $request, ClienteFinal $clientes_finai)
    {
        $cliente = Auth::user()->cliente;
        
        if ($clientes_finai->cliente_id !== $cliente->id) {
            abort(403);
        }

        $validated = $request->validate([
            'nome_empresa' => 'required|string|max:255',
            'nome_responsavel' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cidade' => 'nullable|string|max:255',
            'ultimos_pedidos' => 'nullable|string',
            'data_aniversario_empresa' => 'nullable|date',
            'data_aniversario_responsavel' => 'nullable|date',
        ]);

        $clientes_finai->update($validated);

        return redirect()->route('customer.clientes-finais.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(ClienteFinal $clientes_finai)
    {
        $cliente = Auth::user()->cliente;
        
        if ($clientes_finai->cliente_id !== $cliente->id) {
            abort(403);
        }

        $clientes_finai->delete();

        return redirect()->route('customer.clientes-finais.index')->with('success', 'Cliente removido com sucesso!');
    }
}
