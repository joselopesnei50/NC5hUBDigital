@extends('public.landing-pages.layout')

@section('content')
    {{-- Header Executivo --}}
    <header class="border-b border-slate-100 bg-white/95 backdrop-blur sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3">
                @if($landingPage->logo_path)
                    <img src="{{ asset('storage/' . $landingPage->logo_path) }}" alt="{{ $landingPage->titulo_pagina }}" class="h-10 w-auto object-contain">
                @else
                    <div class="w-10 h-10 rounded-xl bg-lp-primary text-white font-display font-bold flex items-center justify-center text-lg">
                        {{ mb_substr($landingPage->titulo_pagina, 0, 1) }}
                    </div>
                @endif
                <span class="font-display font-bold text-xl text-slate-900">{{ $landingPage->titulo_pagina }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                @if(!empty($landingPage->servicos_json))
                    <a href="#servicos" class="hover:text-slate-900 transition-colors">Soluções</a>
                @endif
                @if($landingPage->sobre_texto)
                    <a href="#sobre" class="hover:text-slate-900 transition-colors">Sobre Nós</a>
                @endif
                @if(!empty($landingPage->depoimentos_json))
                    <a href="#depoimentos" class="hover:text-slate-900 transition-colors">Depoimentos</a>
                @endif
                @if(!empty($landingPage->faq_json))
                    <a href="#faq" class="hover:text-slate-900 transition-colors">Dúvidas</a>
                @endif
                <a href="#contato" class="hover:text-slate-900 transition-colors">Contato</a>
            </nav>

            <div>
                @if($landingPage->whatsappLimpo())
                    <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug]) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lp-primary text-white text-sm font-bold shadow-sm hover:opacity-90 transition-opacity">
                        {{ $landingPage->hero_cta_texto }}
                    </a>
                @else
                    <a href="#contato"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lp-primary text-white text-sm font-bold shadow-sm hover:opacity-90 transition-opacity">
                        {{ $landingPage->hero_cta_texto }}
                    </a>
                @endif
            </div>
        </div>
    </header>

    {{-- Hero Corporativo --}}
    <section class="bg-lp-secondary text-white py-20 lg:py-28 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                @if($landingPage->hero_badge)
                    <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/10 text-white border border-white/15">
                        {{ $landingPage->hero_badge }}
                    </span>
                @endif
                <h1 class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl leading-tight">
                    {{ $landingPage->hero_titulo }}
                </h1>
                @if($landingPage->hero_subtitulo)
                    <p class="text-slate-300 text-lg leading-relaxed max-w-2xl">
                        {{ $landingPage->hero_subtitulo }}
                    </p>
                @endif
                <div class="pt-2 flex flex-wrap items-center gap-4">
                    @if($landingPage->whatsappLimpo())
                        <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug]) }}"
                           target="_blank" rel="noopener"
                           class="px-7 py-4 rounded-full bg-lp-primary text-white font-bold text-base shadow-lg hover:opacity-95 transition-opacity">
                            {{ $landingPage->hero_cta_texto }}
                        </a>
                    @endif
                    <a href="#contato"
                       class="px-7 py-4 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-base border border-white/15 transition-colors">
                        Solicitar Proposta
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                @if($landingPage->hero_image_path)
                    <img src="{{ asset('storage/' . $landingPage->hero_image_path) }}"
                         alt="{{ $landingPage->titulo_pagina }}"
                         class="w-full rounded-3xl shadow-2xl border border-white/10 object-cover max-h-[460px]">
                @else
                    <div class="p-8 rounded-3xl bg-white/5 border border-white/10 backdrop-blur space-y-6">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Por que escolher a {{ $landingPage->titulo_pagina }}?</p>
                        <div class="space-y-4">
                            @foreach(($landingPage->diferenciais_json ?? []) as $dif)
                                <div class="flex items-start gap-3.5">
                                    <div class="w-8 h-8 rounded-lg bg-lp-primary text-white flex items-center justify-center flex-shrink-0 font-bold text-sm">✓</div>
                                    <div>
                                        <h3 class="font-bold text-white text-base">{{ $dif['titulo'] ?? '' }}</h3>
                                        <p class="text-sm text-slate-300 mt-0.5">{{ $dif['descricao'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Diferenciais em Barra (se houver imagem no hero) --}}
    @if($landingPage->hero_image_path && !empty($landingPage->diferenciais_json))
        <section class="py-12 bg-slate-50 border-b border-slate-200/70">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($landingPage->diferenciais_json as $dif)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-lp-primary text-white flex items-center justify-center font-bold flex-shrink-0">✓</div>
                        <div>
                            <h3 class="font-display font-bold text-slate-900">{{ $dif['titulo'] ?? '' }}</h3>
                            <p class="text-sm text-slate-600 mt-1">{{ $dif['descricao'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Soluções / Serviços --}}
    @if(!empty($landingPage->servicos_json))
        <section id="servicos" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl mb-14">
                    <p class="text-xs font-bold uppercase tracking-widest text-lp-primary mb-2">Nossas Especialidades</p>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900">Soluções desenhadas para o seu resultado</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($landingPage->servicos_json as $serv)
                        <div class="p-7 rounded-3xl border border-slate-200/80 hover:border-slate-300 bg-white shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                @if(!empty($serv['destaque']))
                                    <span class="inline-block text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-slate-100 text-lp-primary mb-4">
                                        {{ $serv['destaque'] }}
                                    </span>
                                @endif
                                <h3 class="font-display font-bold text-xl text-slate-900 mb-2">{{ $serv['titulo'] ?? '' }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ $serv['descricao'] ?? '' }}</p>
                            </div>
                            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                                @if(!empty($serv['preco']))
                                    <span class="font-display font-extrabold text-lg text-slate-900">{{ $serv['preco'] }}</span>
                                @else
                                    <span class="text-xs font-semibold text-slate-400">Sob consulta</span>
                                @endif
                                @if($landingPage->whatsappLimpo())
                                    <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug, 'msg' => 'Olá! Gostaria de saber mais sobre: ' . ($serv['titulo'] ?? '')]) }}"
                                       target="_blank" rel="noopener"
                                       class="text-sm font-bold text-lp-primary hover:underline">
                                        Consultar →
                                    </a>
                                @else
                                    <a href="#contato" class="text-sm font-bold text-lp-primary hover:underline">Consultar →</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Sobre Nós --}}
    @if($landingPage->sobre_texto)
        <section id="sobre" class="py-20 bg-slate-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/70 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-widest text-lp-primary mb-2">Quem Somos</p>
                    <h2 class="font-display font-extrabold text-3xl text-slate-900 mb-6">
                        {{ $landingPage->sobre_titulo ?: 'Sobre a ' . $landingPage->titulo_pagina }}
                    </h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed whitespace-pre-line">
                        {{ $landingPage->sobre_texto }}
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Depoimentos --}}
    @if(!empty($landingPage->depoimentos_json))
        <section id="depoimentos" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <p class="text-xs font-bold uppercase tracking-widest text-lp-primary mb-2">Prova Social</p>
                    <h2 class="font-display font-extrabold text-3xl text-slate-900">O que nossos clientes dizem</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($landingPage->depoimentos_json as $dep)
                        <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/60 flex flex-col justify-between">
                            <div>
                                <div class="text-amber-400 text-sm mb-3">
                                    {{ str_repeat('★', (int) ($dep['estrelas'] ?? 5)) }}
                                </div>
                                <p class="text-slate-700 text-sm italic leading-relaxed">"{{ $dep['texto'] ?? '' }}"</p>
                            </div>
                            <div class="mt-5 pt-4 border-t border-slate-200/60">
                                <p class="font-bold text-slate-900 text-sm">{{ $dep['nome'] ?? '' }}</p>
                                @if(!empty($dep['cargo']))
                                    <p class="text-xs text-slate-500">{{ $dep['cargo'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    @if(!empty($landingPage->faq_json))
        <section id="faq" class="py-20 bg-slate-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-xs font-bold uppercase tracking-widest text-lp-primary mb-2">Perguntas Frequentes</p>
                    <h2 class="font-display font-extrabold text-3xl text-slate-900">Tire suas dúvidas</h2>
                </div>
                <div class="space-y-4">
                    @foreach($landingPage->faq_json as $item)
                        <details class="group bg-white rounded-2xl border border-slate-200/80 p-5">
                            <summary class="cursor-pointer list-none font-bold text-slate-900 flex items-center justify-between gap-4">
                                <span>{{ $item['pergunta'] ?? '' }}</span>
                                <span class="text-lp-primary font-extrabold group-open:rotate-45 transition-transform">+</span>
                            </summary>
                            <p class="mt-3 text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $item['resposta'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Contato & Captura de Lead --}}
    <section id="contato" class="py-20 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-5 space-y-6">
                <p class="text-xs font-bold uppercase tracking-widest text-lp-primary">Fale Conosco</p>
                <h2 class="font-display font-extrabold text-3xl text-slate-900">Pronto para dar o próximo passo?</h2>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Preencha o formulário ao lado ou chame diretamente em nossos canais de atendimento.
                </p>

                <div class="space-y-3 pt-2 text-sm text-slate-700">
                    @if($landingPage->telefone)
                        <p><strong>Telefone:</strong> {{ $landingPage->telefone }}</p>
                    @endif
                    @if($landingPage->email_contato)
                        <p><strong>E-mail:</strong> {{ $landingPage->email_contato }}</p>
                    @endif
                    @if($landingPage->endereco_texto)
                        <p><strong>Endereço:</strong> {{ $landingPage->endereco_texto }}</p>
                    @endif
                    @if($landingPage->horario_atendimento)
                        <p><strong>Horário:</strong> {{ $landingPage->horario_atendimento }}</p>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
                    @if(session('lead_success'))
                        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
                            {{ session('lead_success') }}
                        </div>
                    @endif

                    <form action="{{ route('public.landing-page.lead', ['slug' => $landingPage->slug]) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Seu Nome *</label>
                                <input type="text" name="nome" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" placeholder="Nome completo">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">WhatsApp / Telefone *</label>
                                <input type="text" name="telefone" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" placeholder="(00) 00000-0000">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">E-mail</label>
                                <input type="email" name="email" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" placeholder="seu@email.com">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Cidade</label>
                                <input type="text" name="cidade" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" placeholder="Sua cidade">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Como podemos ajudar?</label>
                            <textarea name="mensagem" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" placeholder="Conte brevemente sua necessidade..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-4 rounded-xl bg-lp-primary text-white font-bold text-sm shadow-md hover:opacity-95 transition-opacity">
                            Enviar Solicitação
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-lp-secondary text-slate-400 py-10 text-center text-xs">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>© {{ date('Y') }} {{ $landingPage->titulo_pagina }}. Todos os direitos reservados.</p>
            <div class="flex items-center gap-4">
                @if($landingPage->instagram_url)
                    <a href="{{ $landingPage->instagram_url }}" target="_blank" rel="noopener" class="hover:text-white">Instagram</a>
                @endif
                @if($landingPage->facebook_url)
                    <a href="{{ $landingPage->facebook_url }}" target="_blank" rel="noopener" class="hover:text-white">Facebook</a>
                @endif
            </div>
        </div>
    </footer>
@endsection
