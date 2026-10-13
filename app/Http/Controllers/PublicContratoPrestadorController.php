<?php

namespace App\Http\Controllers;

use App\Models\ContratoPrestador;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PublicContratoPrestadorController extends Controller
{
    public function show(string $token)
    {
        $contrato = ContratoPrestador::with('prestador')
            ->where('signature_token', $token)
            ->firstOrFail();

        return view('public.contrato-prestador.sign', compact('contrato'));
    }

    public function sign(Request $request, string $token)
    {
        $contrato = ContratoPrestador::where('signature_token', $token)->firstOrFail();

        if ($contrato->status_assinatura === 'assinado') {
            return back()->with('error', 'Este contrato já foi assinado.');
        }

        $request->validate([
            'aceite'          => 'accepted',
            'nome_assinante'  => 'required|string|max:255',
            'documento'       => 'required|string|max:30',
            'signature_image' => 'nullable|string',
        ], [
            'aceite.accepted' => 'É necessário aceitar os termos do contrato.',
            'nome_assinante.required' => 'Informe seu nome completo.',
            'documento.required' => 'Informe seu CPF ou CNPJ.',
        ]);

        $signatureData = [
            'nome'            => $request->input('nome_assinante'),
            'documento'       => $request->input('documento'),
            'ip'              => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'timestamp'       => now()->toDateTimeString(),
            'signature_image' => $request->input('signature_image') ?: null,
        ];

        $contrato->update([
            'status_assinatura' => 'assinado',
            'status'            => 'assinado',
            'assinado_em'       => now(),
            'assinatura_url'    => json_encode($signatureData),
        ]);

        return redirect()->route('public.contrato-prestador.show', $token)
            ->with('success', 'Contrato assinado eletronicamente. Uma cópia em PDF pode ser baixada abaixo.');
    }

    public function downloadPdf(string $token)
    {
        $contrato = ContratoPrestador::with('prestador')
            ->where('signature_token', $token)
            ->firstOrFail();

        $pdf = Pdf::loadView('admin.contratos-prestadores.pdf', compact('contrato'))->setPaper('a4');
        return $pdf->download("contrato-prestador-{$contrato->id}.pdf");
    }
}
