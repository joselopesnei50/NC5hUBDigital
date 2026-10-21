<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LancamentoFinanceiro;
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
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%")
                  ->orWhereHas('clienteFinal', function ($q2) use ($search) {
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
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|string|in:Orçamento,Aguardando Pagamento,Em Andamento,Concluído,Cancelado',
            'data_pedido' => 'nullable|date',
            'data_entrega' => 'nullable|date',
            'itens' => 'required|array|min:1',
            'itens.*.produto_servico_id' => 'nullable|exists:produtos_servicos,id',
            'itens.*.nome_item' => 'required|string|max:255',
            'itens.*.quantidade' => 'required|numeric|min:0.01',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
        ]);

        $cliente = Auth::user()->cliente;

        $clienteFinalExists = $cliente->clientesFinais()->where('id', $validated['cliente_final_id'])->exists();
        if (!$clienteFinalExists) {
            abort(403, 'Acesso não autorizado a este cliente.');
        }

        $valorTotalGeral = 0;
        foreach ($validated['itens'] as $item) {
            $valorTotalGeral += ($item['quantidade'] * $item['valor_unitario']);
        }

        $pedido = $cliente->pedidosClientesFinais()->create([
            'cliente_final_id' => $validated['cliente_final_id'],
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'],
            'status' => $validated['status'],
            'data_pedido' => $validated['data_pedido'],
            'data_entrega' => $validated['data_entrega'],
            'valor' => $valorTotalGeral,
        ]);

        foreach ($validated['itens'] as $item) {
            $pedido->itens()->create([
                'produto_servico_id' => $item['produto_servico_id'] ?? null,
                'nome_item' => $item['nome_item'],
                'quantidade' => $item['quantidade'],
                'valor_unitario' => $item['valor_unitario'],
                'valor_total' => $item['quantidade'] * $item['valor_unitario'],
            ]);
        }

        $this->sincronizarLancamentoFinanceiro($pedido);

        return redirect()->route('customer.pedidos.index')->with('success', 'Pedido cadastrado com sucesso!');
    }

    public function edit(PedidoCliente $pedido)
    {
        $cliente = Auth::user()->cliente;

        if ($pedido->cliente_id !== $cliente->id) {
            abort(403);
        }

        $pedido->load('itens');
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
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|string|in:Orçamento,Aguardando Pagamento,Em Andamento,Concluído,Cancelado',
            'data_pedido' => 'nullable|date',
            'data_entrega' => 'nullable|date',
            'itens' => 'required|array|min:1',
            'itens.*.produto_servico_id' => 'nullable|exists:produtos_servicos,id',
            'itens.*.nome_item' => 'required|string|max:255',
            'itens.*.quantidade' => 'required|numeric|min:0.01',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
        ]);

        $clienteFinalExists = $cliente->clientesFinais()->where('id', $validated['cliente_final_id'])->exists();
        if (!$clienteFinalExists) {
            abort(403, 'Acesso não autorizado a este cliente.');
        }

        $valorTotalGeral = 0;
        foreach ($validated['itens'] as $item) {
            $valorTotalGeral += ($item['quantidade'] * $item['valor_unitario']);
        }

        $pedido->update([
            'cliente_final_id' => $validated['cliente_final_id'],
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'],
            'status' => $validated['status'],
            'data_pedido' => $validated['data_pedido'],
            'data_entrega' => $validated['data_entrega'],
            'valor' => $valorTotalGeral,
        ]);

        $pedido->itens()->delete();
        foreach ($validated['itens'] as $item) {
            $pedido->itens()->create([
                'produto_servico_id' => $item['produto_servico_id'] ?? null,
                'nome_item' => $item['nome_item'],
                'quantidade' => $item['quantidade'],
                'valor_unitario' => $item['valor_unitario'],
                'valor_total' => $item['quantidade'] * $item['valor_unitario'],
            ]);
        }

        $this->sincronizarLancamentoFinanceiro($pedido);

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

        if (!$pedido->clienteFinal?->email) {
            return back()->with('error', 'O cliente final não possui um e-mail cadastrado.');
        }

        \Illuminate\Support\Facades\Mail::to($pedido->clienteFinal->email)->send(new \App\Mail\PropostaPedidoMail($pedido));

        return back()->with('success', 'E-mail com o link da proposta enviado com sucesso para ' . $pedido->clienteFinal->email . '!');
    }

    public function kanban()
    {
        $cliente = Auth::user()->cliente;

        $pedidos = $cliente->pedidosClientesFinais()->with('clienteFinal')->orderBy('updated_at', 'desc')->get();

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

        $this->sincronizarLancamentoFinanceiro($pedido);

        return response()->json(['success' => true, 'message' => 'Status atualizado']);
    }

    public function downloadPdf(PedidoCliente $pedido)
    {
        $cliente = Auth::user()->cliente;

        if ($pedido->cliente_id !== $cliente->id) {
            abort(403);
        }

        $pedido->load('itens', 'cliente', 'clienteFinal');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.pedido_cliente', compact('pedido'));
        return $pdf->download("Pedido_{$pedido->id}.pdf");
    }

    /**
     * Sincroniza automaticamente o pedido com Contas a Receber no módulo Financeiro.
     */
    protected function sincronizarLancamentoFinanceiro(PedidoCliente $pedido): void
    {
        if ((float) $pedido->valor <= 0) {
            return;
        }

        $prefixo = "Pedido #{$pedido->id} —";
        $descricaoCompleta = "Pedido #{$pedido->id} — {$pedido->titulo}";

        $lancamento = LancamentoFinanceiro::where('cliente_id', $pedido->cliente_id)
            ->where('tipo', 'receber')
            ->where('descricao', 'like', "{$prefixo}%")
            ->first();

        if (in_array($pedido->status, ['Aguardando Pagamento', 'Em Andamento', 'Concluído'], true)) {
            $novoStatusFinanceiro = $pedido->status === 'Concluído' ? 'pago' : 'pendente';

            if (!$lancamento) {
                LancamentoFinanceiro::create([
                    'cliente_id' => $pedido->cliente_id,
                    'cliente_final_id' => $pedido->cliente_final_id,
                    'tipo' => 'receber',
                    'status' => $novoStatusFinanceiro,
                    'descricao' => $descricaoCompleta,
                    'valor' => $pedido->valor,
                    'data_vencimento' => $pedido->data_entrega ?? now()->addDays(5)->toDateString(),
                    'data_pagamento' => $novoStatusFinanceiro === 'pago' ? now()->toDateString() : null,
                    'categoria' => 'Proposta Comercial',
                ]);
            } else {
                $dadosUpdate = [
                    'cliente_final_id' => $pedido->cliente_final_id,
                    'descricao' => $descricaoCompleta,
                    'valor' => $pedido->valor,
                ];

                if ($pedido->status === 'Concluído' && $lancamento->status !== 'pago') {
                    $dadosUpdate['status'] = 'pago';
                    $dadosUpdate['data_pagamento'] = now()->toDateString();
                }

                $lancamento->update($dadosUpdate);
            }
        } elseif ($pedido->status === 'Cancelado' && $lancamento && $lancamento->status === 'pendente') {
            $lancamento->update(['status' => 'cancelado']);
        }
    }
}
