@extends('public.landing-pages.layout')

@section('content')
    {{-- Hero Split-Screen com Captura Acima da Dobra --}}
    <section class="bg-lp-secondary text-white min-h-[85vh] flex items-center py-14 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-3">
                    @if($landingPage->logo_path)
                        <img src="{{ asset('storage/' . $landingPage->logo_path) }}" alt="{{ $landingPage->titulo_pagina }}" class="h-10 w-auto bg-white p-1 rounded-lg">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-lp-primary text-white font-display font-bold flex items-center justify-center text-lg">
                            {{ mb_substr($landingPage->titulo_pagina, 0, 1) }}
                        </div>
                    @endif
                    <span class="font-display font-bold text-xl">{{ $landingPage->titulo_pagina }}</span>
                </div>
                @if($landingPage->telefone)
                    <span class="text-xs sm:text-sm font-bold text-slate-300">Atendimento: {{ $landingPage->telefone }}</span>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    @if($landingPage->hero_badge)
                        <span class="inline-block px-4 py-1.5 rounded-full bg-lp-primary text-white text-xs font-extrabold uppercase tracking-wider">
                            {{ $landingPage->hero_badge }}
                        </span>
                    @endif
                    <h1 class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl leading-tight">
                        {{ $landingPage->hero_titulo }}
                    </h1>
                    @if($landingPage->hero_subtitulo)
                        <p class="text-slate-300 text-lg leading-relaxed">
                            {{ $landingPage->hero_subtitulo }}
                        </p>
                    @endif

                    @if(!empty($landingPage->diferenciais_json))
                        <div class="pt-4 space-y-3">
                            @foreach($landingPage->diferenciais_json as $dif)
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">✓</span>
                                    <span class="text-slate-200 text-sm sm:text-base">
                                        <strong class="text-white">{{ $dif['titulo'] ?? '' }}:</strong> {{ $dif['descricao'] ?? '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Card de Captura Imediata (Above the fold) --}}
                <div class="lg:col-span-5" id="contato">
                    <div class="bg-white text-slate-900 rounded-3xl p-7 sm:p-8 shadow-2xl border-4 border-white/20">
                        <span class="text-[11px] font-extrabold uppercase tracking-widest text-lp-primary">Atendimento Prioritário</span>
                        <h2 class="font-display font-extrabold text-2xl text-slate-900 mt-1 mb-2">{{ $landingPage->hero_cta_texto }}</h2>
                        <p class="text-xs text-slate-500 mb-6">Preencha os campos abaixo para receber nosso contato personalizado:</p>

                        @if(session('lead_success'))
                            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold">
                                {{ session('lead_success') }}
                            </div>
                        @endif

                        <form action="{{ route('public.landing-page.lead', ['slug' => $landingPage->slug]) }}" method="POST" class="space-y-3.5">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nome Completo *</label>
                                <input type="text" name="nome" required placeholder="Digite seu nome" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">WhatsApp com DDD *</label>
                                <input type="text" name="telefone" required placeholder="(00) 00000-0000" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">E-mail</label>
                                <input type="email" name="email" placeholder="seu@email.com" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">O que você precisa?</label>
                                <textarea name="mensagem" rows="2" placeholder="Descreva rapidamente..." class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm"></textarea>
                            </div>
                            <button type="submit" class="w-full py-4 rounded-xl bg-lp-primary text-white font-extrabold text-base shadow-lg hover:opacity-95 transition-opacity">
                                {{ $landingPage->hero_cta_texto }} →
                            </button>
                            <p class="text-[11px] text-center text-slate-400">🔒 Seus dados estão 100% seguros conosco.</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Serviços / Benefícios --}}
    @if(!empty($landingPage->servicos_json))
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900">O que entregamos para você</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($landingPage->servicos_json as $serv)
                        <div class="p-7 rounded-3xl bg-slate-50 border border-slate-200/80">
                            <h3 class="font-display font-bold text-xl text-slate-900 mb-2">{{ $serv['titulo'] ?? '' }}</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $serv['descricao'] ?? '' }}</p>
                            @if(!empty($serv['preco']))
                                <p class="mt-4 font-display font-extrabold text-lg text-lp-primary">{{ $serv['preco'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Depoimentos + FAQ --}}
    @if(!empty($landingPage->faq_json))
        <section class="py-16 bg-slate-50">
            <div class="max-w-3xl mx-auto px-4">
                <h2 class="font-display font-extrabold text-3xl text-slate-900 text-center mb-10">Perguntas Frequentes</h2>
                <div class="space-y-3">
                    @foreach($landingPage->faq_json as $f)
                        <details class="bg-white rounded-2xl border border-slate-200 p-5">
                            <summary class="cursor-pointer font-bold text-slate-900">{{ $f['pergunta'] ?? '' }}</summary>
                            <p class="mt-2 text-sm text-slate-600">{{ $f['resposta'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <footer class="bg-lp-secondary text-slate-400 py-8 text-center text-xs">
        <p>© {{ date('Y') }} {{ $landingPage->titulo_pagina }}.</p>
    </footer>
@endsection
