@extends('public.landing-pages.layout')

@section('content')
    {{-- Header Vitrine --}}
    <header class="bg-lp-secondary text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3">
                @if($landingPage->logo_path)
                    <img src="{{ asset('storage/' . $landingPage->logo_path) }}" alt="{{ $landingPage->titulo_pagina }}" class="h-10 w-auto object-contain bg-white rounded-lg p-1">
                @else
                    <div class="w-10 h-10 rounded-xl bg-lp-primary text-white font-display font-extrabold flex items-center justify-center text-lg">
                        {{ mb_substr($landingPage->titulo_pagina, 0, 1) }}
                    </div>
                @endif
                <span class="font-display font-bold text-xl">{{ $landingPage->titulo_pagina }}</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="#catalogo" class="hidden sm:inline-block text-sm font-semibold text-slate-300 hover:text-white">Ver Catálogo</a>
                @if($landingPage->whatsappLimpo())
                    <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug]) }}"
                       target="_blank" rel="noopener"
                       class="px-5 py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs sm:text-sm font-bold">
                        Pedir pelo WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </header>

    {{-- Banner Promocional Vitrine --}}
    <section class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-14">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-8 space-y-4">
                @if($landingPage->hero_badge)
                    <span class="inline-block px-3 py-1 rounded-md bg-lp-primary text-white text-xs font-extrabold uppercase tracking-wider">
                        {{ $landingPage->hero_badge }}
                    </span>
                @endif
                <h1 class="font-display font-extrabold text-3xl sm:text-5xl leading-tight">{{ $landingPage->hero_titulo }}</h1>
                @if($landingPage->hero_subtitulo)
                    <p class="text-slate-300 text-base sm:text-lg max-w-2xl">{{ $landingPage->hero_subtitulo }}</p>
                @endif
            </div>
            <div class="lg:col-span-4 flex lg:justify-end">
                <a href="#catalogo" class="px-8 py-4 rounded-2xl bg-lp-primary text-white font-extrabold text-base shadow-lg hover:opacity-95">
                    Ver Ofertas do Catálogo ↓
                </a>
            </div>
        </div>
    </section>

    {{-- Grade de Produtos / Serviços com Botão Direto pro WhatsApp --}}
    <section id="catalogo" class="py-16 bg-slate-50" x-data="{ busca: '' }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
                <div>
                    <h2 class="font-display font-extrabold text-3xl text-slate-900">Catálogo de Produtos & Serviços</h2>
                    <p class="text-sm text-slate-500 mt-1">Escolha o item desejado e faça seu pedido direto pelo WhatsApp.</p>
                </div>
                <div class="w-full sm:w-72">
                    <input type="search" x-model="busca" placeholder="Buscar no catálogo..."
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach(($landingPage->servicos_json ?? []) as $item)
                    <div x-show="!busca || {{ json_encode(mb_strtolower(($item['titulo'] ?? '') . ' ' . ($item['descricao'] ?? ''))) }}.includes(busca.toLowerCase())"
                         class="bg-white rounded-3xl border border-slate-200/90 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                @if(!empty($item['destaque']))
                                    <span class="px-3 py-1 rounded-full bg-slate-100 text-lp-primary text-[11px] font-extrabold uppercase">
                                        {{ $item['destaque'] }}
                                    </span>
                                @else
                                    <span></span>
                                @endif
                                @if(!empty($item['preco']))
                                    <span class="font-display font-extrabold text-xl text-emerald-600">{{ $item['preco'] }}</span>
                                @endif
                            </div>
                            <h3 class="font-display font-bold text-xl text-slate-900 mb-2">{{ $item['titulo'] ?? '' }}</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $item['descricao'] ?? '' }}</p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100">
                            @if($landingPage->whatsappLimpo())
                                <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug, 'msg' => 'Olá! Quero pedir / saber mais sobre o item: ' . ($item['titulo'] ?? '') . (!empty($item['preco']) ? ' (' . $item['preco'] . ')' : '')]) }}"
                                   target="_blank" rel="noopener"
                                   class="w-full py-3 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm flex items-center justify-center gap-2 transition-colors">
                                    <span>Pedir no WhatsApp</span>
                                </a>
                            @else
                                <a href="#contato" class="w-full py-3 px-4 rounded-xl bg-lp-primary text-white font-bold text-sm flex items-center justify-center">
                                    Solicitar Orçamento
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Formulário de Orçamento Personalizado --}}
    <section id="contato" class="py-16 bg-white border-t border-slate-200/70">
        <div class="max-w-4xl mx-auto px-4">
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200">
                <h2 class="font-display font-extrabold text-2xl text-slate-900 mb-2">Precisa de um orçamento personalizado?</h2>
                <p class="text-sm text-slate-600 mb-6">Deixe seus dados e lista de itens que entraremos em contato.</p>

                @if(session('lead_success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
                        {{ session('lead_success') }}
                    </div>
                @endif

                <form action="{{ route('public.landing-page.lead', ['slug' => $landingPage->slug]) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="text" name="nome" required placeholder="Seu nome *" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                        <input type="text" name="telefone" required placeholder="Seu WhatsApp *" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                    </div>
                    <textarea name="mensagem" rows="3" placeholder="Quais produtos ou serviços você deseja orçar?" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></textarea>
                    <button type="submit" class="px-8 py-4 rounded-xl bg-lp-primary text-white font-bold text-sm">
                        Enviar Pedido de Orçamento
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-lp-secondary text-slate-400 py-8 text-center text-xs">
        <p>© {{ date('Y') }} {{ $landingPage->titulo_pagina }}. {{ $landingPage->endereco_texto }}</p>
    </footer>
@endsection
