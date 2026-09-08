<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PedidoCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoClienteController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $search = $request->input('search');

        $query = $cliente->pedidosClientesFinais()->with('clienteFinal');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%")
                  ->orWhereHas('clienteFinal', function($q2) use ($search) {
                      $q2->where('nome_empresa', 'like', "%{$search}%")
                         ->orWhere('nome_responsavel', 'like', "%{$search}%");
                  });
            });
        }

        $pedidos = $query->latest()->paginate(10);

        return view('customer.pedidos_clientes.index', compact('pedidos', 'search'));
    }

    public function create()
    {
        $cliente = Auth::user()->cliente;
        $clientesFinais = $cliente->clientesFinais()->orderBy('nome_empresa')->get();
        return view('customer.pedidos_clientes.create', compact('clientesFinais'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_final_id' => 'required|exists:clientes_finais,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'status' => 'required|string|in:Orçamento,Aguardando Pagamento,Em Andamento,Concluído,Cancelado',
            'data_pedido' => 'nullable|date',
            'data_entrega' => 'nullable|date',
        ]);

        $cliente = Auth::user()->cliente;
        
        // Verifica se o cliente_final_id pertence realmente a este cliente
        $clienteFinalExists = $cliente->clientesFinais()->where('id', $validated['cliente_final_id'])->exists();
        if (!$clienteFinalExists) {
            abort(403, 'Acesso não autorizado a este cliente.');
        }

        $cliente->pedidosClientesFinais()->create($validated);

        return redirect()->route('customer.pedidos.index')->with('success', 'Pedido cadastrado com sucesso!');
    }

    public function edit(PedidoCliente $pedido)
    {
        $cliente = Auth::user()->cliente;
        
        if ($pedido->cliente_id !== $cliente->id) {
            abort(403);
        }

        $clientesFinais = $cliente->clientesFinais()->orderBy('nome_empresa')->get();
        return view('customer.pedidos_clientes.edit', compact('pedido', 'clientesFinais'));
    }

    public function update(Request $request, PedidoCliente $pedido)
    {
        $cliente = Auth::user()->cliente;
        
        if ($pedido->cliente_id !== $cliente->id) {
            abort(403);
        }

        $validated = $request->validate([
            'cliente_final_id' => 'required|exists:clientes_finais,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'status' => 'required|string|in:Orçamento,Aguardando Pagamento,Em Andamento,Concluído,Cancelado',
            'data_pedido' => 'nullable|date',
            'data_entrega' => 'nullable|date',
        ]);

        $clienteFinalExists = $cliente->clientesFinais()->where('id', $validated['cliente_final_id'])->exists();
        if (!$clienteFinalExists) {
            abort(403, 'Acesso não autorizado a este cliente.');
        }

        $pedido->update($validated);

        return redirect()->route('customer.pedidos.index')->with('success', 'Pedido atualizado com sucesso!');
    }

    public function destroy(PedidoCliente $pedido)
    {
        $cliente = Auth::user()->cliente;
        
        if ($pedido->cliente_id !== $cliente->id) {
            abort(403);
        }

        $pedido->delete();

        return redirect()->route('customer.pedidos.index')->with('success', 'Pedido excluído com sucesso!');
    }
}
