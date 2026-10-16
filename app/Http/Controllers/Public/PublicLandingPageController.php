<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ClienteFinal;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicLandingPageController extends Controller
{
    protected function findVisibleLandingPage(string $slug): LandingPage
    {
        $lp = LandingPage::with('cliente')->where('slug', $slug)->firstOrFail();

        if (!$lp->is_published) {
            $user = Auth::user();
            $userClienteId = $user?->cliente?->id ?? $user?->cliente_id;
            $isAdmin = $user && $user->role === 'admin';

            if (!$isAdmin && (int) $userClienteId !== (int) $lp->cliente_id) {
                abort(404, 'Esta página ainda não está publicada.');
            }
        }

        return $lp;
    }

    public function show(string $slug)
    {
        $landingPage = $this->findVisibleLandingPage($slug);

        // Incrementa contador de visitas (apenas se não for o próprio dono logado em preview)
        $user = Auth::user();
        $userClienteId = $user?->cliente?->id ?? $user?->cliente_id;
        if ((int) $userClienteId !== (int) $landingPage->cliente_id) {
            $landingPage->increment('views_count');
        }

        $template = array_key_exists($landingPage->template, LandingPage::TEMPLATES)
            ? $landingPage->template
            : 'corporativo';

        return view("public.landing-pages.templates.{$template}", compact('landingPage'));
    }

    public function storeLead(Request $request, string $slug)
    {
        $landingPage = $this->findVisibleLandingPage($slug);

        $validated = $request->validate([
            'nome' => 'required|string|max:140',
            'telefone' => 'required|string|max:40',
            'email' => 'nullable|email|max:160',
            'cidade' => 'nullable|string|max:100',
            'mensagem' => 'nullable|string|max:1000',
        ], [
            'nome.required' => 'Por favor, informe seu nome.',
            'telefone.required' => 'Por favor, informe seu telefone ou WhatsApp.',
        ]);

        $resumoOrigem = '[Lead via Landing Page: ' . $landingPage->titulo_pagina . ' em ' . now()->format('d/m/Y H:i') . ']';
        if (!empty($validated['mensagem'])) {
            $resumoOrigem .= ' Mensagem: ' . trim($validated['mensagem']);
        }

        ClienteFinal::create([
            'cliente_id' => $landingPage->cliente_id,
            'nome_empresa' => $validated['nome'],
            'nome_responsavel' => $validated['nome'],
            'telefone' => $validated['telefone'],
            'email' => $validated['email'] ?? null,
            'cidade' => $validated['cidade'] ?? null,
            'ultimos_pedidos' => $resumoOrigem,
        ]);

        $landingPage->increment('leads_count');

        return redirect()
            ->to(route('public.landing-page.show', ['slug' => $landingPage->slug]) . '#contato')
            ->with('lead_success', 'Recebemos seus dados com sucesso! Nossa equipe entrará em contato em breve.');
    }

    public function redirectWhatsapp(Request $request, string $slug)
    {
        $landingPage = $this->findVisibleLandingPage($slug);
        $landingPage->increment('whatsapp_clicks_count');

        $msg = $request->query('msg');
        $url = $landingPage->whatsappUrl(is_string($msg) && $msg !== '' ? $msg : null);

        if (!$url) {
            return redirect()->route('public.landing-page.show', ['slug' => $landingPage->slug]);
        }

        return redirect()->away($url);
    }
}
