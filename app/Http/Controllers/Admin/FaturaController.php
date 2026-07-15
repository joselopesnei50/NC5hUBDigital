<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Services\AbacatePayService;

class FaturaController extends Controller
{
    public function index()
    {
        $faturas = Fatura::with('cliente')->paginate(10);
        return view('admin.faturas.index', compact('faturas'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('admin.faturas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric',
            'vencimento' => 'required|date',
            'descricao' => 'required|string|max:255',
        ]);

        $fatura = Fatura::create([
            'cliente_id' => $request->cliente_id,
            'valor' => $request->valor,
            'vencimento' => $request->vencimento,
            'descricao' => $request->descricao,
            'status' => 'pendente'
        ]);

        try {
            $abacate = new AbacatePayService();
            $valorCentavos = (int) ($fatura->valor * 100);
            
            // Criar Produto na AbacatePay
            $produtoId = $abacate->criarProduto($fatura->descricao, $valorCentavos, $fatura->id);
            
            if ($produtoId) {
                // Criar Checkout
                $link = $abacate->criarCheckout($produtoId, $fatura->id);
                if ($link) {
                    $fatura->update(['link_pagamento' => $link]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao integrar AbacatePay na fatura ' . $fatura->id . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.faturas.index')->with('success', 'Fatura gerada com sucesso!');
    }

    public function show($id)
    {
        $fatura = Fatura::with('cliente.user')->findOrFail($id);
        return view('admin.faturas.show', compact('fatura'));
    }

    public function edit($id)
    {
        $fatura = Fatura::findOrFail($id);
        $clientes = Cliente::all();
        return view('admin.faturas.edit', compact('fatura', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        $fatura = Fatura::findOrFail($id);

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric',
            'vencimento' => 'required|date',
            'descricao' => 'required|string|max:255',
            'status' => 'required|in:pendente,pago,cancelado,atrasado',
        ]);

        $fatura->update($request->only(['cliente_id', 'valor', 'vencimento', 'descricao', 'status']));

        return redirect()->route('admin.faturas.index')->with('success', 'Fatura atualizada.');
    }

    public function destroy($id)
    {
        Fatura::findOrFail($id)->delete();
        return redirect()->route('admin.faturas.index')->with('success', 'Fatura removida.');
    }
}
