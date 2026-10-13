<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContratoPrestadorMail;
use App\Models\ContratoPrestador;
use App\Models\Prestador;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContratoPrestadorController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = ContratoPrestador::with('prestador')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $contratos = $query->paginate(15)->withQueryString();

        return view('admin.contratos-prestadores.index', compact('contratos', 'status'));
    }

    public function create(Request $request)
    {
        $prestadores = Prestador::ativos()->orderBy('nome')->get();
        $prestadorSelecionado = $request->integer('prestador_id') ?: null;

        return view('admin.contratos-prestadores.create', compact('prestadores', 'prestadorSelecionado'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateContrato($request);

        $contrato = ContratoPrestador::create($validated + [
            'status' => 'rascunho',
            'status_assinatura' => 'pendente',
        ]);

        return redirect()->route('admin.contratos-prestadores.show', $contrato)
            ->with('success', 'Contrato criado. Revise e envie para o prestador.');
    }

    public function show(ContratoPrestador $contrato)
    {
        $contrato = $contrato->load('prestador');
        return view('admin.contratos-prestadores.show', compact('contrato'));
    }

    public function edit(ContratoPrestador $contrato)
    {
        $prestadores = Prestador::ativos()->orderBy('nome')->get();
        return view('admin.contratos-prestadores.edit', compact('contrato', 'prestadores'));
    }

    public function update(Request $request, ContratoPrestador $contrato)
    {
        $validated = $this->validateContrato($request, true);

        $contrato->update($validated);

        return redirect()->route('admin.contratos-prestadores.show', $contrato)
            ->with('success', 'Contrato atualizado.');
    }

    public function destroy(ContratoPrestador $contrato)
    {
        $contrato->delete();

        return redirect()->route('admin.contratos-prestadores.index')
            ->with('success', 'Contrato removido.');
    }

    public function enviarEmail(ContratoPrestador $contrato)
    {
        $contrato->loadMissing('prestador');

        $email = $contrato->prestador->email ?? null;
        if (!$email) {
            return back()->with('error', 'O prestador não possui e-mail cadastrado.');
        }

        $contrato->ensureSignatureToken();

        try {
            Mail::to($email)->send(new ContratoPrestadorMail($contrato));

            $contrato->update([
                'enviado_em' => now(),
                'status'     => $contrato->status === 'rascunho' ? 'enviado' : $contrato->status,
            ]);

            return back()->with('success', "Contrato enviado para {$email}. Link público: " . $contrato->publicSignUrl());
        } catch (\Throwable $e) {
            Log::error("Falha ao enviar contrato prestador #{$contrato->id}: " . $e->getMessage());
            return back()->with('error', 'Não foi possível enviar o e-mail: ' . $e->getMessage());
        }
    }

    public function downloadPdf(ContratoPrestador $contrato)
    {
        $contrato = $contrato->load('prestador');
        $pdf = Pdf::loadView('admin.contratos-prestadores.pdf', compact('contrato'))->setPaper('a4');
        return $pdf->download("contrato-prestador-{$contrato->id}.pdf");
    }

    protected function validateContrato(Request $request, bool $withStatus = false): array
    {
        $rules = [
            'prestador_id'                       => 'required|exists:prestadores,id',
            'titulo'                             => 'required|string|max:255',
            'objeto'                             => 'required|string',
            'valor'                              => 'required|numeric|min:0',
            'forma_pagamento'                    => 'required|in:mensal,por_entrega,unico,hora,outro',
            'periodicidade'                      => 'nullable|string|max:60',
            'data_inicio'                        => 'required|date',
            'data_fim'                           => 'nullable|date|after_or_equal:data_inicio',
            'prazo_entrega'                      => 'nullable|string|max:120',
            'clausula_confidencialidade'         => 'nullable|boolean',
            'clausula_exclusividade'             => 'nullable|boolean',
            'clausula_propriedade_intelectual'   => 'nullable|boolean',
            'clausula_nao_vinculo_empregaticio'  => 'nullable|boolean',
            'clausulas_extras'                   => 'nullable|string',
            'conteudo'                           => 'nullable|string',
        ];

        if ($withStatus) {
            $rules['status'] = 'required|in:rascunho,enviado,assinado,cancelado,encerrado';
        }

        $data = $request->validate($rules);

        foreach (['clausula_confidencialidade', 'clausula_exclusividade', 'clausula_propriedade_intelectual', 'clausula_nao_vinculo_empregaticio'] as $flag) {
            $data[$flag] = (bool) $request->input($flag, false);
        }

        return $data;
    }
}
