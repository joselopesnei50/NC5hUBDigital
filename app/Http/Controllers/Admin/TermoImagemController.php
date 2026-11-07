<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TermoImagemMail;
use App\Models\Cliente;
use App\Models\TermoImagem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TermoImagemController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $clienteId = $request->input('cliente_id');

        $query = TermoImagem::with('cliente')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($clienteId === 'nc5') {
            $query->whereNull('cliente_id');
        } elseif ($clienteId) {
            $query->where('cliente_id', (int) $clienteId);
        }

        $termos = $query->paginate(15)->withQueryString();
        $clientes = Cliente::orderBy('razao_social')->get();

        return view('admin.termos-imagem.index', compact('termos', 'status', 'clienteId', 'clientes'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('razao_social')->get();
        $clienteSelecionado = $request->integer('cliente_id') ?: null;
        $templateAdulto = TermoImagem::defaultTemplate(false);
        $templateMenor = TermoImagem::defaultTemplate(true);

        return view('admin.termos-imagem.create', compact(
            'clientes',
            'clienteSelecionado',
            'templateAdulto',
            'templateMenor'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTermo($request);

        $validated['conteudo'] = $this->resolveConteudo($validated);
        $validated['status'] = 'rascunho';
        $validated['signature_token'] = Str::random(48);

        $termo = TermoImagem::create($validated);

        return redirect()->route('admin.termos-imagem.show', $termo)
            ->with('success', 'Termo de uso de imagem criado com sucesso.');
    }

    public function show(TermoImagem $termo)
    {
        $termo->load('cliente');
        $termo->ensureSignatureToken();

        return view('admin.termos-imagem.show', compact('termo'));
    }

    public function edit(TermoImagem $termo)
    {
        if ($termo->isAssinado()) {
            return redirect()->route('admin.termos-imagem.show', $termo)
                ->with('error', 'Este termo já foi assinado e não pode mais ser editado.');
        }

        $clientes = Cliente::orderBy('razao_social')->get();
        $templateAdulto = TermoImagem::defaultTemplate(false);
        $templateMenor = TermoImagem::defaultTemplate(true);

        return view('admin.termos-imagem.edit', compact(
            'termo',
            'clientes',
            'templateAdulto',
            'templateMenor'
        ));
    }

    public function update(Request $request, TermoImagem $termo)
    {
        if ($termo->isAssinado()) {
            return redirect()->route('admin.termos-imagem.show', $termo)
                ->with('error', 'Este termo já foi assinado e não pode mais ser editado.');
        }

        $validated = $this->validateTermo($request, true);
        $validated['conteudo'] = $this->resolveConteudo($validated);

        $termo->update($validated);

        return redirect()->route('admin.termos-imagem.show', $termo)
            ->with('success', 'Termo de uso de imagem atualizado.');
    }

    public function cancelar(TermoImagem $termo)
    {
        if ($termo->status === 'cancelado') {
            return back()->with('error', 'Este termo já está cancelado.');
        }

        $termo->update(['status' => 'cancelado']);

        return redirect()->route('admin.termos-imagem.show', $termo)
            ->with('success', 'Termo cancelado.');
    }

    public function destroy(TermoImagem $termo)
    {
        $termo->delete();

        return redirect()->route('admin.termos-imagem.index')
            ->with('success', 'Termo de uso de imagem removido.');
    }

    public function enviarEmail(TermoImagem $termo)
    {
        $termo->loadMissing('cliente');

        if ($termo->isAssinado() || $termo->status === 'cancelado') {
            return back()->with('error', 'Não é possível enviar e-mail para um termo já assinado ou cancelado.');
        }

        $email = $termo->cedente_email;
        if (!$email) {
            return back()->with('error', 'O cedente não possui e-mail cadastrado neste termo.');
        }

        $termo->ensureSignatureToken();

        try {
            Mail::to($email)->send(new TermoImagemMail($termo));

            $termo->update([
                'enviado_em' => now(),
                'status'     => $termo->status === 'rascunho' ? 'enviado' : $termo->status,
            ]);

            return back()->with('success', "Termo enviado para {$email}. Link público: " . $termo->publicSignUrl());
        } catch (\Throwable $e) {
            Log::error("Falha ao enviar termo de imagem #{$termo->id}: " . $e->getMessage());
            return back()->with('error', 'Não foi possível enviar o e-mail: ' . $e->getMessage());
        }
    }

    public function downloadPdf(TermoImagem $termo)
    {
        $termo->load('cliente');
        $pdf = Pdf::loadView('admin.termos-imagem.pdf', compact('termo'))->setPaper('a4');

        return $pdf->download("termo-imagem-{$termo->id}.pdf");
    }

    protected function validateTermo(Request $request, bool $withStatus = false): array
    {
        $menor = $request->boolean('cedente_menor');

        $rules = [
            'cliente_id'             => 'nullable|exists:clientes,id',
            'titulo'                 => 'required|string|max:255',
            'projeto_referencia'     => 'nullable|string|max:255',
            'data_captacao'          => 'nullable|date',
            'local_captacao'         => 'nullable|string|max:255',
            'cedente_nome'           => 'required|string|max:255',
            'cedente_cpf'            => 'required|string|max:30',
            'cedente_rg'             => 'nullable|string|max:40',
            'cedente_email'          => 'nullable|email|max:255',
            'cedente_telefone'       => 'nullable|string|max:40',
            'cedente_endereco'       => 'nullable|string|max:255',
            'cedente_menor'          => 'nullable|boolean',
            'responsavel_nome'       => $menor ? 'required|string|max:255' : 'nullable|string|max:255',
            'responsavel_cpf'        => $menor ? 'required|string|max:30' : 'nullable|string|max:30',
            'responsavel_parentesco' => $menor ? 'required|string|max:60' : 'nullable|string|max:60',
            'conteudo'               => 'nullable|string',
        ];

        if ($withStatus) {
            $rules['status'] = 'required|in:rascunho,enviado,cancelado';
        }

        $messages = [
            'responsavel_nome.required'       => 'Para cedente menor de idade, informe o nome completo do responsável legal.',
            'responsavel_cpf.required'        => 'Para cedente menor de idade, informe o CPF do responsável legal.',
            'responsavel_parentesco.required' => 'Para cedente menor de idade, informe o vínculo/parentesco do responsável legal.',
        ];

        $data = $request->validate($rules, $messages);
        $data['cedente_menor'] = $menor;

        if (!$menor) {
            $data['responsavel_nome'] = null;
            $data['responsavel_cpf'] = null;
            $data['responsavel_parentesco'] = null;
        }

        if (empty($data['cliente_id'])) {
            $data['cliente_id'] = null;
        }

        return $data;
    }

    protected function resolveConteudo(array $validated): string
    {
        $menor = (bool) ($validated['cedente_menor'] ?? false);
        $rawConteudo = trim((string) ($validated['conteudo'] ?? ''));

        if ($rawConteudo === '') {
            $rawConteudo = TermoImagem::defaultTemplate($menor);
        }

        $clienteNome = null;
        if (!empty($validated['cliente_id'])) {
            $clienteNome = Cliente::where('id', $validated['cliente_id'])->value('razao_social');
        }

        return TermoImagem::replacePlaceholders($rawConteudo, $validated + [
            'cliente_nome' => $clienteNome,
        ]);
    }
}
