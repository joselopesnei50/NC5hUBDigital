<?php

namespace App\Http\Controllers;

use App\Models\AgentAlert;
use App\Models\LancamentoFinanceiro;
use App\Models\PedidoCliente;
use Illuminate\Http\Request;

class PublicPedidoController extends Controller
{
    public function show($token)
    {
        $pedido = PedidoCliente::with(['itens', 'cliente', 'clienteFinal', 'produtoServico'])
            ->where('token_publico', $token)
            ->firstOrFail();

        return view('public.pedido', compact('pedido'));
    }

    public function approve(Request $request, $token)
    {
        $pedido = PedidoCliente::with(['cliente', 'clienteFinal'])
            ->where('token_publico', $token)
            ->firstOrFail();

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

        // 1. Gerar automaticamente lançamento em Contas a Receber (se ainda não existir para este pedido)
        $descricaoLancamento = "Pedido #{$pedido->id} — {$pedido->titulo}";
        $jaExisteLancamento = LancamentoFinanceiro::where('cliente_id', $pedido->cliente_id)
            ->where('descricao', $descricaoLancamento)
            ->exists();

        if (!$jaExisteLancamento && (float) $pedido->valor > 0) {
            LancamentoFinanceiro::create([
                'cliente_id' => $pedido->cliente_id,
                'cliente_final_id' => $pedido->cliente_final_id,
                'tipo' => 'receber',
                'status' => 'pendente',
                'descricao' => $descricaoLancamento,
                'valor' => $pedido->valor,
                'data_vencimento' => $pedido->data_entrega ?? now()->addDays(5)->toDateString(),
                'categoria' => 'Proposta Comercial',
            ]);
        }

        // 2. Emitir alerta proativo do BruceOS para o cliente NC5
        $nomeClienteFinal = $pedido->clienteFinal?->nome_empresa
            ?: ($pedido->clienteFinal?->nome_responsavel ?? 'Cliente');
        $valorFormatado = number_format((float) $pedido->valor, 2, ',', '.');

        AgentAlert::create([
            'cliente_id' => $pedido->cliente_id,
            'tipo' => 'proposta_aprovada',
            'severidade' => 'info',
            'titulo' => "Proposta #{$pedido->id} aprovada por {$nomeClienteFinal}!",
            'mensagem' => "O orçamento \"{$pedido->titulo}\" no valor de R$ {$valorFormatado} foi assinado digitalmente por {$request->nome_aprovacao}. O título já foi lançado automaticamente em Contas a Receber.",
            'detalhes' => [
                'pedido_id' => $pedido->id,
                'valor' => (float) $pedido->valor,
                'aprovado_por' => $request->nome_aprovacao,
                'ip' => $request->ip(),
            ],
        ]);

        return back()->with('success', 'Proposta aprovada eletronicamente com sucesso! A empresa responsável já foi notificada.');
    }

    public function downloadPdf($token)
    {
        $pedido = PedidoCliente::with(['itens', 'cliente', 'clienteFinal'])
            ->where('token_publico', $token)
            ->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.pedido_cliente', compact('pedido'));
        return $pdf->download("Proposta_{$pedido->id}.pdf");
    }
}
