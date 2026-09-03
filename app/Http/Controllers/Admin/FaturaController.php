<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FaturaGeradaMail;
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

    /**
     * Converte valor em formato BRL (1.500,00) para decimal (1500.00)
     */
    private function parseBrlValue($valor)
    {
        // Remove "R$", espaços, pontos de milhar e converte vírgula decimal
        $valor = preg_replace('/[R$\s]/', '', $valor);
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        return (float) $valor;
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|string',
            'vencimento' => 'required|date',
            'descricao' => 'required|string|max:255',
            'forma_pagamento' => 'nullable|string|in:pix,boleto,transferencia,link_pagamento',
            'nota_fiscal' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'observacoes' => 'nullable|string',
            // Dados bancários
            'banco' => 'nullable|string|max:100',
            'agencia' => 'nullable|string|max:20',
            'conta' => 'nullable|string|max:30',
            'tipo_chave_pix' => 'nullable|string|in:cpf,cnpj,email,telefone,aleatoria',
            'chave_pix' => 'nullable|string|max:255',
            'titular' => 'nullable|string|max:255',
        ]);

        $valorDecimal = $this->parseBrlValue($request->valor);

        // Upload da nota fiscal
        $notaFiscalPath = null;
        if ($request->hasFile('nota_fiscal')) {
            $notaFiscalPath = $request->file('nota_fiscal')->store('notas_fiscais', 'public');
        }

        // Montar dados bancários como JSON
        $dadosBancarios = null;
        if ($request->forma_pagamento && $request->forma_pagamento !== 'link_pagamento') {
            $dadosBancarios = array_filter([
                'banco' => $request->banco,
                'agencia' => $request->agencia,
                'conta' => $request->conta,
                'tipo_chave_pix' => $request->tipo_chave_pix,
                'chave_pix' => $request->chave_pix,
                'titular' => $request->titular,
            ]);
        }

        $fatura = Fatura::create([
            'cliente_id' => $request->cliente_id,
            'valor' => $valorDecimal,
            'vencimento' => $request->vencimento,
            'descricao' => $request->descricao,
            'status' => 'pendente',
            'forma_pagamento' => $request->forma_pagamento,
            'nota_fiscal_path' => $notaFiscalPath,
            'dados_bancarios' => $dadosBancarios,
            'observacoes' => $request->observacoes,
        ]);

        // Se for link de pagamento, integrar com AbacatePay
        if ($request->forma_pagamento === 'link_pagamento') {
            try {
                $abacate = new AbacatePayService();
                $valorCentavos = (int) ($valorDecimal * 100);

                $produtoId = $abacate->criarProduto($fatura->descricao, $valorCentavos, $fatura->id);

                if ($produtoId) {
                    $link = $abacate->criarCheckout($produtoId, $fatura->id);
                    if ($link) {
                        $fatura->update(['link_pagamento' => $link]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao integrar AbacatePay na fatura ' . $fatura->id . ': ' . $e->getMessage());
            }
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
            'valor' => 'required|string',
            'vencimento' => 'required|date',
            'descricao' => 'required|string|max:255',
            'status' => 'required|in:pendente,pago,cancelado,atrasado',
            'forma_pagamento' => 'nullable|string|in:pix,boleto,transferencia,link_pagamento',
            'nota_fiscal' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'observacoes' => 'nullable|string',
            'banco' => 'nullable|string|max:100',
            'agencia' => 'nullable|string|max:20',
            'conta' => 'nullable|string|max:30',
            'tipo_chave_pix' => 'nullable|string|in:cpf,cnpj,email,telefone,aleatoria',
            'chave_pix' => 'nullable|string|max:255',
            'titular' => 'nullable|string|max:255',
        ]);

        $valorDecimal = $this->parseBrlValue($request->valor);

        $data = [
            'cliente_id' => $request->cliente_id,
            'valor' => $valorDecimal,
            'vencimento' => $request->vencimento,
            'descricao' => $request->descricao,
            'status' => $request->status,
            'forma_pagamento' => $request->forma_pagamento,
            'observacoes' => $request->observacoes,
        ];

        // Upload da nota fiscal (substituir se existir)
        if ($request->hasFile('nota_fiscal')) {
            $data['nota_fiscal_path'] = $request->file('nota_fiscal')->store('notas_fiscais', 'public');
        }

        // Montar dados bancários
        if ($request->forma_pagamento && $request->forma_pagamento !== 'link_pagamento') {
            $data['dados_bancarios'] = array_filter([
                'banco' => $request->banco,
                'agencia' => $request->agencia,
                'conta' => $request->conta,
                'tipo_chave_pix' => $request->tipo_chave_pix,
                'chave_pix' => $request->chave_pix,
                'titular' => $request->titular,
            ]);
        }

        $fatura->update($data);

        return redirect()->route('admin.faturas.index')->with('success', 'Fatura atualizada.');
    }

    public function destroy($id)
    {
        Fatura::findOrFail($id)->delete();
        return redirect()->route('admin.faturas.index')->with('success', 'Fatura removida.');
    }

    public function enviarEmail($id)
    {
        $fatura = Fatura::with('cliente.user')->findOrFail($id);
        
        if ($fatura->cliente && $fatura->cliente->user) {
            Mail::to($fatura->cliente->user->email)->send(new FaturaGeradaMail($fatura));
            return back()->with('success', 'E-mail enviado com sucesso para o cliente!');
        }

        return back()->with('error', 'Não foi possível enviar o e-mail: cliente não possui usuário vinculado.');
    }
}
