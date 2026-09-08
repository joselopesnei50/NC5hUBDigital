<?php

namespace App\Http\Controllers;

use App\Models\PedidoCliente;
use Illuminate\Http\Request;

class PublicPedidoController extends Controller
{
    public function show($token)
    {
        $pedido = PedidoCliente::with(['cliente', 'clienteFinal', 'produtoServico'])
            ->where('token_publico', $token)
            ->firstOrFail();

        return view('public.pedido', compact('pedido'));
    }

    public function approve(Request $request, $token)
    {
        $pedido = PedidoCliente::where('token_publico', $token)->firstOrFail();

        if ($pedido->status !== 'Orçamento') {
            return back()->with('error', 'Este pedido não está mais na fase de orçamento ou já foi aprovado.');
        }

        $request->validate([
            'nome_aprovacao' => 'required|string|max:255',
        ]);

        $pedido->update([
            'status' => 'Aguardando Pagamento',
            'nome_aprovacao' => $request->nome_aprovacao,
            'ip_aprovacao' => $request->ip(),
            'data_aprovacao' => now(),
        ]);

        return back()->with('success', 'Orçamento aprovado com sucesso! A empresa foi notificada.');
    }
}
