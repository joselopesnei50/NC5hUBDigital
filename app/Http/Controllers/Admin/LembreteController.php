<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Fatura;
use App\Models\Contrato;
use App\Models\Briefing;
use App\Models\Material;
use App\Mail\LembreteAcao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LembreteController extends Controller
{
    public function create()
    {
        // Carrega clientes com usuário associado (para e-mail)
        $clientesQuery = Cliente::with('user')->where('status', 'ativo')->get();
        
        $clientes = [];

        foreach ($clientesQuery as $cliente) {
            $faturas = Fatura::where('cliente_id', $cliente->id)->where('status', 'pendente')->get();
            $contratos = Contrato::where('cliente_id', $cliente->id)->where('status_assinatura', 'pendente')->get();
            $briefings = Briefing::where('cliente_id', $cliente->id)->where('status', 'pendente')->get();
            $materiais = Material::where('cliente_id', $cliente->id)->where('status_aprovacao', 'pendente')->get();

            $acoesPendentes = [];

            foreach ($faturas as $f) {
                $acoesPendentes[] = [
                    'tipo' => 'fatura',
                    'id' => $f->id,
                    'descricao' => 'Fatura pendente: ' . $f->descricao . ' - R$ ' . number_format($f->valor, 2, ',', '.'),
                    'assunto' => 'Aviso de Fatura Pendente',
                    'mensagem' => "Olá {$cliente->user->name},\n\nNotamos que você possui uma fatura pendente referente a: {$f->descricao}.\nPor favor, acesse o painel para realizar o pagamento.",
                    'link' => route('customer.invoices'),
                    'botao' => 'Pagar Fatura'
                ];
            }

            foreach ($contratos as $c) {
                $acoesPendentes[] = [
                    'tipo' => 'contrato',
                    'id' => $c->id,
                    'descricao' => 'Contrato pendente de assinatura: ' . $c->titulo,
                    'assunto' => 'Assinatura de Contrato Pendente',
                    'mensagem' => "Olá {$cliente->user->name},\n\nO contrato \"{$c->titulo}\" está aguardando sua assinatura eletrônica para darmos andamento aos serviços.\nAcesse a sua área de cliente para assinar.",
                    'link' => route('customer.contracts'),
                    'botao' => 'Assinar Contrato'
                ];
            }

            foreach ($briefings as $b) {
                $acoesPendentes[] = [
                    'tipo' => 'briefing',
                    'id' => $b->id,
                    'descricao' => 'Briefing pendente: ' . $b->titulo,
                    'assunto' => 'Precisamos das suas respostas',
                    'mensagem' => "Olá {$cliente->user->name},\n\nPara avançarmos com o seu projeto, precisamos que você preencha o briefing \"{$b->titulo}\".\nLeva apenas alguns minutinhos!",
                    'link' => route('customer.briefings'),
                    'botao' => 'Responder Briefing'
                ];
            }

            foreach ($materiais as $m) {
                $acoesPendentes[] = [
                    'tipo' => 'material',
                    'id' => $m->id,
                    'descricao' => 'Material para aprovar: ' . $m->titulo,
                    'assunto' => 'Novo Material para sua Aprovação',
                    'mensagem' => "Olá {$cliente->user->name},\n\nNossa equipe finalizou a criação do material \"{$m->titulo}\" e ele está pronto para a sua avaliação.\nAcesse o painel para aprovar ou solicitar ajustes.",
                    'link' => route('customer.materiais'),
                    'botao' => 'Avaliar Material'
                ];
            }

            $clientes[] = [
                'id' => $cliente->id,
                'nome' => $cliente->user->name,
                'email' => $cliente->user->email,
                'pendencias' => $acoesPendentes
            ];
        }

        return view('admin.lembretes.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'link_acao' => 'nullable|url',
            'texto_botao' => 'nullable|string|max:50',
        ]);

        $cliente = Cliente::with('user')->findOrFail($request->cliente_id);
        $email = $cliente->user->email;

        // O texto do botão padrão caso o usuário não envie um
        $botao = $request->texto_botao ?: 'Acessar Área do Cliente';
        $link = $request->link_acao ?: route('customer.index'); // link fallback pro dashboard do cliente

        $apiKey = \App\Models\Configuracao::get('brevo_api_key');

        if (!empty($apiKey)) {
            // Usa a API do Brevo diretamente
            $htmlContent = view('emails.lembrete-acao', [
                'assunto' => $request->assunto,
                'mensagem' => $request->mensagem,
                'linkAcao' => $request->filled('link_acao') ? $request->link_acao : null,
                'textoBotao' => $botao
            ])->render();

            $senderEmail = \App\Models\Configuracao::get('mail_from_address', 'contato@nc5.com.br');
            $senderName = \App\Models\Configuracao::get('mail_from_name', 'NC5 Hub');

            try {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'api-key' => $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => ['name' => $senderName, 'email' => $senderEmail],
                    'to' => [['email' => $email, 'name' => $cliente->user->name]],
                    'subject' => $request->assunto,
                    'htmlContent' => $htmlContent
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Erro enviando lembrete via Brevo: " . $e->getMessage());
            }
        } else {
            // Dispara o e-mail via Mail (fallback se não tiver Brevo configurado)
            Mail::to($email)->send(new LembreteAcao(
                $request->assunto,
                $request->mensagem,
                $request->filled('link_acao') ? $request->link_acao : null, // Só envia link se quiser mostrar botão
                $botao
            ));
        }

        return redirect()->route('admin.lembretes.create')
                         ->with('success', 'Lembrete enviado com sucesso para ' . $cliente->user->name . '!');
    }
}
