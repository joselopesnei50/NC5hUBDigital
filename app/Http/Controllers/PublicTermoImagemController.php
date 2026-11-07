<?php

namespace App\Http\Controllers;

use App\Models\TermoImagem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PublicTermoImagemController extends Controller
{
    public function show(string $token)
    {
        $termo = TermoImagem::with('cliente')
            ->where('signature_token', $token)
            ->firstOrFail();

        return view('public.termo-imagem.sign', compact('termo'));
    }

    public function sign(Request $request, string $token)
    {
        $termo = TermoImagem::where('signature_token', $token)->firstOrFail();

        if ($termo->status === 'assinado') {
            return back()->with('error', 'Este termo já foi assinado anteriormente.');
        }

        if ($termo->status === 'cancelado') {
            return back()->with('error', 'Este termo foi cancelado e não pode mais ser assinado.');
        }

        $request->validate([
            'aceite'          => 'accepted',
            'nome_assinante'  => 'required|string|max:255',
            'documento'       => 'required|string|max:30',
            'signature_image' => [
                'required',
                'string',
                function (string $attribute, $value, \Closure $fail) {
                    if (!is_string($value) || !str_starts_with($value, 'data:image/png;base64,')) {
                        $fail('Desenhe sua assinatura no quadro acima antes de confirmar.');
                        return;
                    }

                    $base64 = substr($value, strlen('data:image/png;base64,'));
                    $decoded = base64_decode($base64, true);

                    if ($decoded === false) {
                        $fail('Formato de imagem da assinatura inválido.');
                        return;
                    }

                    $pngSignature = "\x89PNG\r\n\x1a\n";
                    if (strlen($decoded) < 60 || strlen($decoded) > 2 * 1024 * 1024 || !str_starts_with($decoded, $pngSignature)) {
                        $fail('Assinatura manuscrita inválida ou em branco. Por favor, desenhe sua assinatura.');
                    }
                },
            ],
        ], [
            'aceite.accepted'          => 'É necessário declarar que leu e concorda com os termos.',
            'nome_assinante.required'  => 'Informe o nome completo de quem está assinando.',
            'documento.required'       => 'Informe o CPF de quem está assinando.',
            'signature_image.required' => 'A assinatura manuscrita desenhada na tela é obrigatória.',
        ]);

        $signatureData = [
            'nome'            => $request->input('nome_assinante'),
            'documento'       => $request->input('documento'),
            'ip'              => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'timestamp'       => now()->toDateTimeString(),
            'signature_image' => $request->input('signature_image'),
        ];

        $termo->update([
            'status'           => 'assinado',
            'assinado_em'      => now(),
            'assinatura_dados' => json_encode($signatureData, JSON_UNESCAPED_UNICODE),
            'conteudo_hash'    => hash('sha256', (string) $termo->conteudo),
        ]);

        return redirect()->route('public.termo-imagem.show', $token)
            ->with('success', 'Termo assinado eletronicamente com sucesso! Você pode baixar sua cópia em PDF abaixo.');
    }

    public function downloadPdf(string $token)
    {
        $termo = TermoImagem::with('cliente')
            ->where('signature_token', $token)
            ->firstOrFail();

        $pdf = Pdf::loadView('admin.termos-imagem.pdf', compact('termo'))->setPaper('a4');

        return $pdf->download("termo-imagem-{$termo->id}.pdf");
    }
}
