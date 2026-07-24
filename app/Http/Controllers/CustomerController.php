<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MaterialAvaliadoMail;

class CustomerController extends Controller
{
    public function index()
    {
        $cliente = Auth::user()->cliente;
        if (!$cliente) {
            abort(403, 'Acesso negado. Sua conta não está vinculada a um cliente.');
        }

        $faturasPendentes = $cliente->faturas()->where('status', 'pendente')->count();
        $materiaisAguardando = $cliente->materiais()->where('status_aprovacao', 'pendente')->count();
        $contratosPendentes = $cliente->contratos()->where('status_assinatura', '!=', 'assinado')->count();

        return view('customer.index', compact('cliente', 'faturasPendentes', 'materiaisAguardando', 'contratosPendentes'));
    }

    public function contracts()
    {
        $cliente = Auth::user()->cliente;
        $contratos = $cliente->contratos()->with('servico')->latest()->get();
        return view('customer.contracts', compact('contratos'));
    }

    public function signContract(Request $request, $id)
    {
        $cliente = Auth::user()->cliente;
        
        $contrato = $cliente->contratos()->findOrFail($id);
        
        if ($contrato->status_assinatura === 'assinado') {
            return back()->with('error', 'Este contrato já foi assinado.');
        }

        $signatureData = [
            'ip'              => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'timestamp'       => now()->toDateTimeString(),
            'signature_image' => $request->signature_image ?: null,
        ];

        // Guardamos o registro de assinatura na URL (como string JSON) apenas para auditoria, 
        // já que o banco possui a coluna assinatura_url que podemos aproveitar para log leve.
        $contrato->update([
            'status_assinatura' => 'assinado',
            'assinatura_url' => json_encode($signatureData),
            'status' => 'ativo' // Ativa o contrato após a assinatura
        ]);

        return back()->with('success', 'Contrato assinado digitalmente com sucesso!');
    }

    public function downloadContract($id)
    {
        $cliente = Auth::user()->cliente;
        $contrato = $cliente->contratos()->with(['servico', 'cliente.user'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.contratos.pdf', compact('contrato'))->setPaper('a4');
        return $pdf->download("contrato-{$contrato->id}.pdf");
    }

    public function invoices()
    {
        $cliente = Auth::user()->cliente;
        $faturas = $cliente->faturas()->latest()->get();
        return view('customer.invoices', compact('faturas'));
    }

    public function support()
    {
        $cliente = Auth::user()->cliente;
        $tickets = $cliente->tickets()->latest()->get();
        return view('customer.support', compact('tickets'));
    }

    public function storeTicket(Request $request)
    {
        $request->validate([
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string'
        ]);

        $cliente = Auth::user()->cliente;

        $ticket = \App\Models\Ticket::create([
            'cliente_id' => $cliente->id,
            'assunto' => $request->assunto,
            'status' => 'aberto'
        ]);

        \App\Models\TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'mensagem' => $request->mensagem
        ]);

        return redirect()->route('customer.support.show', $ticket->id)->with('success', 'Chamado aberto com sucesso!');
    }

    public function showTicket($id)
    {
        $cliente = Auth::user()->cliente;
        $ticket = \App\Models\Ticket::where('cliente_id', $cliente->id)->with('mensagens.user')->findOrFail($id);

        return view('customer.ticket_show', compact('ticket'));
    }

    public function replyTicket(Request $request, $id)
    {
        $request->validate(['mensagem' => 'required|string']);
        
        $cliente = Auth::user()->cliente;
        $ticket = \App\Models\Ticket::where('cliente_id', $cliente->id)->findOrFail($id);

        if ($ticket->status === 'fechado') {
            $ticket->update(['status' => 'aberto']);
        }

        \App\Models\TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'mensagem' => $request->mensagem
        ]);

        return back()->with('success', 'Mensagem enviada com sucesso!');
    }

    public function briefings()
    {
        $cliente = Auth::user()->cliente;
        $briefings = \App\Models\Briefing::where('cliente_id', $cliente->id)->latest()->get();
        return view('customer.briefings', compact('briefings'));
    }

    public function answerBriefing(Request $request, $id)
    {
        $cliente = Auth::user()->cliente;
        $briefing = \App\Models\Briefing::where('cliente_id', $cliente->id)->findOrFail($id);

        $request->validate([
            'resposta_cliente' => 'required|string',
            'anexo_cliente' => 'nullable|file|max:10240'
        ]);

        $path = null;
        if ($request->hasFile('anexo_cliente')) {
            $path = $request->file('anexo_cliente')->store('briefings', 'public');
        }

        $briefing->update([
            'resposta_cliente' => $request->resposta_cliente,
            'anexo_cliente_path' => $path,
            'status' => 'respondido'
        ]);

        return back()->with('success', 'Briefing respondido com sucesso!');
    }

    public function materiais()
    {
        $cliente = Auth::user()->cliente;
        $materiais = $cliente->materiais()->latest()->get();
        return view('customer.materiais', compact('materiais'));
    }

    public function evaluateMaterial(Request $request, $id)
    {
        $request->validate([
            'status_aprovacao' => 'required|in:aprovado,ajustes_solicitados,reprovado',
            'comentario_cliente' => 'nullable|string',
            'anexo_cliente' => 'nullable|file|max:25600',
        ]);

        $cliente = Auth::user()->cliente;
        $material = $cliente->materiais()->findOrFail($id);

        $anexoPath = $material->anexo_cliente_path;
        if ($request->hasFile('anexo_cliente')) {
            $anexoPath = $request->file('anexo_cliente')->store('materials_feedback', 'public');
        }

        $material->update([
            'status_aprovacao' => $request->status_aprovacao,
            'comentario_cliente' => $request->comentario_cliente,
            'anexo_cliente_path' => $anexoPath,
            'data_resposta' => now(),
        ]);

        // Notifica a equipe Admin por e-mail
        try {
            $adminEmail = \App\Models\Configuracao::get('admin_email') ?: config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new MaterialAvaliadoMail($material));
            }
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail de avaliação do material #' . $material->id . ': ' . $e->getMessage());
        }

        return back()->with('success', 'Sua avaliação e observações foram enviadas com sucesso para a equipe NC5 Hub!');
    }
}
