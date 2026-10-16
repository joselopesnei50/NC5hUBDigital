@extends('public.landing-pages.layout')

@section('content')
    {{-- Top Bar com Horário e Endereço Local --}}
    <div class="bg-lp-secondary text-white text-xs py-2.5 px-4">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-4 flex-wrap">
                @if($landingPage->horario_atendimento)
                    <span>🕒 {{ $landingPage->horario_atendimento }}</span>
                @endif
                @if($landingPage->endereco_texto)
                    <span>📍 {{ $landingPage->endereco_texto }}</span>
                @endif
            </div>
            @if($landingPage->telefone)
                <span class="font-bold">📞 {{ $landingPage->telefone }}</span>
            @endif
        </div>
    </div>

    {{-- Navbar Negócio Local --}}
    <header class="bg-white border-b border-slate-100 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3">
                @if($landingPage->logo_path)
                    <img src="{{ asset('storage/' . $landingPage->logo_path) }}" alt="{{ $landingPage->titulo_pagina }}" class="h-11 w-auto object-contain">
                @else
                    <div class="w-11 h-11 rounded-2xl bg-lp-primary text-white font-display font-extrabold flex items-center justify-center text-lg shadow-sm">
                        {{ mb_substr($landingPage->titulo_pagina, 0, 1) }}
                    </div>
                @endif
                <div>
                    <span class="font-display font-extrabold text-lg text-slate-900 block leading-none">{{ $landingPage->titulo_pagina }}</span>
                    <span class="text-[11px] text-emerald-600 font-semibold">● Atendimento Rápido</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                @if($landingPage->whatsappLimpo())
                    <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug]) }}"
                       target="_blank" rel="noopener"
                       class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm shadow-sm transition-colors">
                        Agendar pelo WhatsApp
                    </a>
                @else
                    <a href="#contato" class="px-5 py-2.5 rounded-2xl bg-lp-primary text-white font-bold text-sm">
                        {{ $landingPage->hero_cta_texto }}
                    </a>
                @endif
            </div>
        </div>
    </header>

    {{-- Hero Vibrante Local --}}
    <section class="py-16 lg:py-24 bg-gradient-to-br from-slate-50 to-slate-100/70">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                @if($landingPage->hero_badge)
                    <span class="inline-block px-4 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider bg-lp-primary text-white shadow-sm">
                        {{ $landingPage->hero_badge }}
                    </span>
                @endif
                <h1 class="font-display font-extrabold text-4xl sm:text-5xl text-slate-900 leading-tight">
                    {{ $landingPage->hero_titulo }}
                </h1>
                @if($landingPage->hero_subtitulo)
                    <p class="text-slate-600 text-lg leading-relaxed">
                        {{ $landingPage->hero_subtitulo }}
                    </p>
                @endif

                <div class="pt-3 flex flex-wrap gap-3">
                    @if($landingPage->whatsappLimpo())
                        <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug]) }}"
                           target="_blank" rel="noopener"
                           class="px-8 py-4 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-base shadow-lg transition-all">
                            {{ $landingPage->hero_cta_texto }}
                        </a>
                    @endif
                    <a href="#servicos" class="px-7 py-4 rounded-2xl bg-white border border-slate-200 text-slate-800 font-bold text-base hover:bg-slate-50">
                        Ver Serviços e Preços
                    </a>
                </div>

                @if(!empty($landingPage->diferenciais_json))
                    <div class="pt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($landingPage->diferenciais_json as $dif)
                            <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                                <p class="font-bold text-slate-900 text-xs">✓ {{ $dif['titulo'] ?? '' }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $dif['descricao'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="lg:col-span-5">
                @if($landingPage->hero_image_path)
                    <img src="{{ asset('storage/' . $landingPage->hero_image_path) }}"
                         alt="{{ $landingPage->titulo_pagina }}"
                         class="w-full rounded-3xl shadow-xl object-cover max-h-[440px]">
                @else
                    <div class="bg-white p-7 rounded-3xl shadow-xl border border-slate-100 space-y-4">
                        <div class="flex items-center gap-2 text-amber-400 text-lg font-bold">
                            ★★★★★ <span class="text-xs text-slate-500 font-semibold">(Atendimento 5 Estrelas)</span>
                        </div>
                        <h3 class="font-display font-bold text-xl text-slate-900">Agende seu horário ou tire dúvidas agora</h3>
                        <p class="text-sm text-slate-600">Deixe seu contato abaixo para retornarmos imediatamente:</p>
                        @if(session('lead_success'))
                            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold">
                                {{ session('lead_success') }}
                            </div>
                        @endif
                        <form action="{{ route('public.landing-page.lead', ['slug' => $landingPage->slug]) }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="text" name="nome" required placeholder="Seu nome *" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            <input type="text" name="telefone" required placeholder="Seu WhatsApp *" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            <input type="text" name="mensagem" placeholder="Qual serviço você procura?" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
                            <button type="submit" class="w-full py-3.5 rounded-xl bg-lp-primary text-white font-bold text-sm">
                                Quero Atendimento Rápido
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Lista de Serviços Locais com Botão Rápido --}}
    @if(!empty($landingPage->servicos_json))
        <section id="servicos" class="py-16 bg-white">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center max-w-xl mx-auto mb-12">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-lp-primary">Nossos Serviços</span>
                    <h2 class="font-display font-extrabold text-3xl text-slate-900 mt-1">Confira como podemos atender você</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($landingPage->servicos_json as $serv)
                        <div class="p-6 rounded-2xl border border-slate-200 hover:border-slate-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-50/50">
                            <div class="space-y-1">
                                @if(!empty($serv['destaque']))
                                    <span class="inline-block text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-lp-primary text-white">
                                        {{ $serv['destaque'] }}
                                    </span>
                                @endif
                                <h3 class="font-display font-bold text-lg text-slate-900">{{ $serv['titulo'] ?? '' }}</h3>
                                <p class="text-sm text-slate-600">{{ $serv['descricao'] ?? '' }}</p>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end gap-3 flex-shrink-0 w-full sm:w-auto justify-between">
                                @if(!empty($serv['preco']))
                                    <span class="font-display font-extrabold text-lg text-slate-900">{{ $serv['preco'] }}</span>
                                @endif
                                @if($landingPage->whatsappLimpo())
                                    <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug, 'msg' => 'Olá! Quero agendar/saber mais sobre: ' . ($serv['titulo'] ?? '')]) }}"
                                       target="_blank" rel="noopener"
                                       class="px-4 py-2 rounded-xl bg-lp-primary text-white text-xs font-bold whitespace-nowrap">
                                        Agendar →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Avaliações estilo Google --}}
    @if(!empty($landingPage->depoimentos_json))
        <section class="py-16 bg-slate-50">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-10">
                    Clientes satisfeitos na região
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($landingPage->depoimentos_json as $dep)
                        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-slate-900 text-sm">{{ $dep['nome'] ?? '' }}</span>
                                <span class="text-amber-400 text-sm">{{ str_repeat('★', (int) ($dep['estrelas'] ?? 5)) }}</span>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">"{{ $dep['texto'] ?? '' }}"</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Localização e Contato --}}
    <section id="contato" class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="space-y-4">
                <h2 class="font-display font-extrabold text-3xl text-slate-900">Onde estamos & Horários</h2>
                @if($landingPage->sobre_texto)
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $landingPage->sobre_texto }}</p>
                @endif
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-sm">
                    @if($landingPage->endereco_texto)
                        <p><strong>📍 Endereço:</strong> {{ $landingPage->endereco_texto }}</p>
                    @endif
                    @if($landingPage->horario_atendimento)
                        <p><strong>🕒 Horário:</strong> {{ $landingPage->horario_atendimento }}</p>
                    @endif
                    @if($landingPage->telefone)
                        <p><strong>📞 Telefone:</strong> {{ $landingPage->telefone }}</p>
                    @endif
                </div>
            </div>

            <div class="p-7 rounded-3xl bg-slate-50 border border-slate-200">
                <h3 class="font-display font-bold text-xl text-slate-900 mb-4">Envie uma mensagem</h3>
                <form action="{{ route('public.landing-page.lead', ['slug' => $landingPage->slug]) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="text" name="nome" required placeholder="Seu nome *" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                    <input type="text" name="telefone" required placeholder="Seu telefone / WhatsApp *" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                    <textarea name="mensagem" rows="3" placeholder="Sua dúvida ou horário de preferência..." class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"></textarea>
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-lp-primary text-white font-bold text-sm">
                        Enviar Mensagem
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-lp-secondary text-slate-400 py-8 text-center text-xs">
        <p>© {{ date('Y') }} {{ $landingPage->titulo_pagina }}.</p>
    </footer>
@endsection
