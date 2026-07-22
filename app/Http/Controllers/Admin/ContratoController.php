<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContratoDisponivelMail;
use App\Models\Contrato;
use App\Models\Cliente;
use App\Models\Servico;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::with(['cliente', 'servico'])->paginate(10);
        return view('admin.contratos.index', compact('contratos'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $servicos = Servico::where('status', 'ativo')->get();
        return view('admin.contratos.create', compact('clientes', 'servicos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servico_id' => 'nullable|exists:servicos,id',
            'data_inicio' => 'required|date',
            'data_fim'    => 'nullable|date',
        ]);

        $contrato = Contrato::create([
            'cliente_id' => $request->cliente_id,
            'servico_id' => $request->servico_id ?: null,
            'data_inicio' => $request->data_inicio,
            'data_fim'    => $request->data_fim,
            'conteudo'    => $request->conteudo,
            'status'      => 'ativo',
        ]);

        try {
            $contrato->load(['cliente.user', 'servico']);
            $email = $contrato->cliente->user->email ?? null;
            if ($email) {
                Mail::to($email)->send(new ContratoDisponivelMail($contrato));
            }
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail de contrato #' . $contrato->id . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.contratos.index')->with('success', 'Contrato criado e e-mail enviado ao cliente!');
    }

    public function show($id)
    {
        $contrato = Contrato::with(['cliente.user', 'servico'])->findOrFail($id);
        return view('admin.contratos.show', compact('contrato'));
    }

    public function edit($id)
    {
        $contrato = Contrato::findOrFail($id);
        $clientes = Cliente::all();
        $servicos = Servico::where('status', 'ativo')->get();
        return view('admin.contratos.edit', compact('contrato', 'clientes', 'servicos'));
    }

    public function update(Request $request, $id)
    {
        $contrato = Contrato::findOrFail($id);

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servico_id' => 'nullable|exists:servicos,id',
            'data_inicio' => 'required|date',
            'data_fim'    => 'nullable|date',
            'status'      => 'required|in:ativo,inativo,pendente,cancelado',
        ]);

        $data = $request->only(['cliente_id', 'data_inicio', 'data_fim', 'status', 'conteudo']);
        $data['servico_id'] = $request->servico_id ?: null;

        $contrato->update($data);

        return redirect()->route('admin.contratos.index')->with('success', 'Contrato atualizado.');
    }

    public function destroy($id)
    {
        Contrato::findOrFail($id)->delete();
        return redirect()->route('admin.contratos.index')->with('success', 'Contrato removido.');
    }

    public function downloadPdf($id)
    {
        $contrato = Contrato::with(['cliente.user', 'servico'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.contratos.pdf', compact('contrato'))->setPaper('a4');
        return $pdf->download("contrato-{$contrato->id}.pdf");
    }
}
