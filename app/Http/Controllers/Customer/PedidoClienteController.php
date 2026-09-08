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
        $produtosServicos = $cliente->produtosServicos()->orderBy('tipo')->orderBy('nome')->get();
        return view('customer.pedidos_clientes.create', compact('clientesFinais', 'produtosServicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_final_id' => 'required|exists:clientes_finais,id',
            'produto_servico_id' => 'nullable|exists:produtos_servicos,id',
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

        if(!empty($validated['produto_servico_id'])) {
            $produtoExists = $cliente->produtosServicos()->where('id', $validated['produto_servico_id'])->exists();
            if(!$produtoExists) {
                $validated['produto_servico_id'] = null;
            }
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
        $produtosServicos = $cliente->produtosServicos()->orderBy('tipo')->orderBy('nome')->get();
        return view('customer.pedidos_clientes.edit', compact('pedido', 'clientesFinais', 'produtosServicos'));
    }

    public function update(Request $request, PedidoCliente $pedido)
    {
        $cliente = Auth::user()->cliente;
        
        if ($pedido->cliente_id !== $cliente->id) {
            abort(403);
        }

        $validated = $request->validate([
            'cliente_final_id' => 'required|exists:clientes_finais,id',
            'produto_servico_id' => 'nullable|exists:produtos_servicos,id',
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

        if(!empty($validated['produto_servico_id'])) {
            $produtoExists = $cliente->produtosServicos()->where('id', $validated['produto_servico_id'])->exists();
            if(!$produtoExists) {
                $validated['produto_servico_id'] = null;
            }
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

    public function enviarEmail(PedidoCliente $pedido)
    {
        $cliente = Auth::user()->cliente;
        
        if ($pedido->cliente_id !== $cliente->id) {
            abort(403);
        }

        if (!$pedido->clienteFinal->email) {
            return back()->with('error', 'O cliente final não possui um e-mail cadastrado.');
        }

        \Illuminate\Support\Facades\Mail::to($pedido->clienteFinal->email)->send(new \App\Mail\PropostaPedidoMail($pedido));

        return back()->with('success', 'E-mail com o link da proposta enviado com sucesso para ' . $pedido->clienteFinal->email . '!');
    }

    public function kanban()
    {
        $cliente = Auth::user()->cliente;
        
        // Pega todos os pedidos ativos (exceto cancelados, a menos que você queira mostrar cancelados)
        $pedidos = $cliente->pedidosClientesFinais()->with('clienteFinal')->orderBy('updated_at', 'desc')->get();

        // Agrupa pelos status principais
        $kanban = [
            'Orçamento' => $pedidos->where('status', 'Orçamento'),
            'Aguardando Pagamento' => $pedidos->where('status', 'Aguardando Pagamento'),
            'Em Andamento' => $pedidos->where('status', 'Em Andamento'),
            'Concluído' => $pedidos->where('status', 'Concluído'),
        ];

        return view('customer.pedidos_clientes.kanban', compact('kanban'));
    }

    public function updateStatus(Request $request, PedidoCliente $pedido)
    {
        $cliente = Auth::user()->cliente;
        
        if ($pedido->cliente_id !== $cliente->id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:Orçamento,Aguardando Pagamento,Em Andamento,Concluído,Cancelado'
        ]);

        $pedido->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Status atualizado']);
    }
}
