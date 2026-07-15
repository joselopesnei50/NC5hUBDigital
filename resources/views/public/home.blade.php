@extends('layouts.public')

@section('title', 'NC5 Hub — Estratégia, Design & Performance')

@section('content')

    {{-- =====================================================
         HERO · Banner Bruce fullscreen · motor de mudança
         ===================================================== --}}
    <section class="relative min-h-[calc(100vh-4rem)] bg-bruceInk overflow-hidden flex items-center">

        <!-- glows -->
        <div class="absolute -top-40 right-1/4 w-[800px] h-[600px] bg-bruce/20 rounded-full blur-[160px] pointer-events-none animate-pulse" style="animation-duration: 6s;"></div>
        <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-bruce/12 rounded-full blur-[140px] pointer-events-none"></div>

        <!-- grade sutil -->
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none"
             style="background-image: linear-gradient(#FF7A1A 1px, transparent 1px), linear-gradient(90deg, #FF7A1A 1px, transparent 1px); background-size: 56px 56px;"></div>

        <!-- borda inferior degradê -->
        <div class="absolute bottom-0 inset-x-0 h-40 bg-gradient-to-b from-transparent to-bruceInk pointer-events-none"></div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <!-- COLUNA ESQUERDA · texto -->
                <div class="lg:col-span-7 text-white">


                    <h1 class="font-display font-bold text-4xl sm:text-5xl lg:text-6xl leading-[1.02] tracking-tight text-white">
                        <em class="not-italic text-bruce">IA</em> e <em class="not-italic text-bruce">design</em> para impulsionar seu negócio.
                    </h1>

                    <p class="mt-6 text-base lg:text-lg text-white/70 leading-relaxed max-w-xl">
                        Bruce é a IA da NC5 Hub. Estratégia, design e performance em uma só esteira — para transformar marcas em máquinas de conversão.
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('servicos') }}" class="group inline-flex items-center gap-3 bg-bruce hover:bg-white hover:text-bruceInk text-white pl-6 pr-4 py-4 rounded-full text-base font-bold transition-all shadow-xl shadow-bruce/30">
                            Ver o que fazemos
                            <span class="w-8 h-8 bg-white/20 group-hover:bg-bruceInk/10 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </a>
                        <a href="#metodologia" class="inline-flex items-center gap-3 text-white/80 hover:text-bruce border border-white/15 hover:border-bruce px-6 py-4 rounded-full text-base font-bold transition-colors">
                            Como o Bruce funciona
                        </a>
                    </div>

                    <!-- stats / tags pequenas -->
                    <div class="mt-14 lg:mt-16 pt-10 border-t border-white/10">
                        <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 backdrop-blur-sm px-4 py-2 rounded-full cursor-default hover:bg-white/10 transition-colors">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 2C6.495 2 2 6.495 2 12.031c0 1.761.458 3.486 1.332 5.006L2 22l5.127-1.345c1.472.844 3.146 1.288 4.904 1.288 5.536 0 10.031-4.495 10.031-10.031S17.567 2 12.031 2z"/></svg>
                            <span class="text-xs font-bold text-white/90">Integração com API Oficial WhatsApp</span>
                        </div>
                    </div>
                </div>

                <!-- COLUNA DIREITA · Bruce imponente -->
                <div class="lg:col-span-5 relative flex items-center justify-center min-h-[400px] lg:min-h-[600px]">

                    <!-- halo pulsante atrás do ícone -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-[360px] h-[360px] lg:w-[500px] lg:h-[500px] bg-bruce/30 rounded-full blur-[100px] animate-pulse" style="animation-duration: 4s;"></div>
                    </div>

                    <!-- ícone Bruce gigante centralizado -->
                    <div class="relative flex justify-center items-center">
                        <div class="absolute inset-0 bg-bruce/10 rounded-full blur-[80px] animate-pulse pointer-events-none"></div>
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-64 h-64 lg:w-80 lg:h-80 xl:w-96 xl:h-96 animate-float animate-glow relative z-10">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
         SERVICES
         ===================================================== --}}
    <section class="relative bg-white py-24 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-widest text-bruce mb-4">O que fazemos</p>
                    <h2 class="font-display font-bold text-4xl md:text-5xl text-ink leading-tight">Uma esteira completa, sem fricção entre marca e mídia.</h2>
                </div>
                <a href="{{ route('servicos') }}" class="inline-flex items-center gap-2 text-sm font-bold text-ink hover:text-bruce transition-colors">
                    Ver catálogo completo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($servicos as $index => $servico)
                    <a href="{{ route('servicos') }}" class="group relative bg-mist rounded-3xl p-8 flex flex-col overflow-hidden hover:bg-ink hover:text-white transition-all duration-500">
                        <div class="flex items-start justify-between mb-8">
                            <span class="w-12 h-12 rounded-2xl bg-white group-hover:bg-bruce group-hover:text-white text-ink font-display font-bold flex items-center justify-center text-lg transition-colors">
                                0{{ $index + 1 }}
                            </span>
                            <svg class="w-5 h-5 text-slate group-hover:text-white group-hover:-translate-y-1 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <h3 class="font-display text-2xl font-bold leading-tight mb-3">{{ $servico->nome }}</h3>
                        <p class="text-sm leading-relaxed opacity-70 line-clamp-3">{{ $servico->descricao }}</p>

                    </a>
                @empty
                    <div class="col-span-3 text-center py-16">
                        <p class="text-slate">O catálogo será exibido assim que serviços forem publicados.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =====================================================
         METODOLOGIA
         ===================================================== --}}
    <section id="metodologia" class="relative py-24 lg:py-32 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-16">
                <p class="text-xs font-bold uppercase tracking-widest text-bruce mb-4">Metodologia NC5</p>
                <h2 class="font-display font-bold text-4xl md:text-5xl text-ink leading-tight">Do briefing ao pixel, um único fluxo.</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach([
                    ['n' => '01', 't' => 'Diagnóstico', 'd' => 'Auditoria de marca, funil e mercado. Encontramos a alavanca certa.'],
                    ['n' => '02', 't' => 'Estratégia', 'd' => 'Posicionamento, oferta, criativos e canais desenhados sob medida.'],
                    ['n' => '03', 't' => 'Execução', 'd' => 'Design, tecnologia e mídia rodando na mesma cadência semanal.'],
                    ['n' => '04', 't' => 'Escala', 'd' => 'Otimização contínua guiada por dashboards e testes rápidos.'],
                ] as $step)
                    <div class="group bg-white border border-black/5 rounded-3xl p-8 hover:-translate-y-2 hover:shadow-2xl hover:shadow-ink/5 transition-all duration-500">
                        <p class="font-display font-black text-6xl text-mist group-hover:text-bruce transition-colors">{{ $step['n'] }}</p>
                        <h3 class="mt-6 font-bold text-lg text-ink">{{ $step['t'] }}</h3>
                        <p class="mt-2 text-sm text-slate leading-relaxed">{{ $step['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         INSIGHTS
         ===================================================== --}}
    <section class="relative py-24 lg:py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-widest text-bruce mb-4">Insights</p>
                    <h2 class="font-display font-bold text-4xl md:text-5xl text-ink leading-tight">O que estamos lendo sobre marca, mídia e IA.</h2>
                </div>
                <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm font-bold text-ink hover:text-bruce transition-colors">
                    Ler todos os artigos
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    <a href="{{ route('blog.post', $post->slug) }}" class="group block">
                        <div class="aspect-[4/3] rounded-3xl bg-gradient-to-br from-mist via-white to-mist border border-black/5 flex items-center justify-center overflow-hidden mb-5 group-hover:shadow-2xl group-hover:shadow-ink/5 transition-all">
                            <span class="font-display font-black text-6xl text-slate/20 group-hover:scale-110 group-hover:text-bruce/50 transition-all">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <p class="text-xs font-bold text-bruce uppercase tracking-widest">{{ $post->created_at->format('d M Y') }}</p>
                        <h3 class="mt-2 font-display font-bold text-2xl text-ink group-hover:text-bruce transition-colors line-clamp-2 leading-tight">{{ $post->titulo }}</h3>
                        <p class="mt-3 text-sm text-slate line-clamp-2">{{ Str::limit(strip_tags($post->conteudo), 120) }}</p>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate">Novos artigos em breve.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =====================================================
         CTA PEQUENO · testar o Bruce (antes do rodapé)
         ===================================================== --}}
    <section class="relative pb-16 lg:pb-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden bg-bruceInk rounded-3xl p-6 sm:p-8 lg:p-10 shadow-xl shadow-bruceInk/10">
                <div class="absolute -top-24 -right-24 w-72 h-72 bg-bruce/15 rounded-full blur-[100px] pointer-events-none"></div>

                <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-14 h-14 flex-shrink-0 drop-shadow-[0_0_20px_rgba(255,122,26,0.4)]">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-bruce mb-1.5">Teste grátis</p>
                            <p class="font-display font-bold text-2xl lg:text-3xl text-white leading-tight">Quer ver o Bruce em ação?</p>
                            <p class="text-sm text-white/60 mt-1.5 max-w-lg">Receba um parecer premium sobre sua marca, site ou perfil social em ~30 segundos.</p>
                        </div>
                    </div>
                    <a href="{{ route('analise.index') }}" class="group inline-flex items-center gap-3 bg-bruce hover:bg-white hover:text-bruceInk text-white pl-6 pr-4 py-3.5 rounded-full text-sm font-bold transition-all shadow-lg shadow-bruce/30 flex-shrink-0 whitespace-nowrap">
                        Gerar análise gratuita
                        <span class="w-7 h-7 bg-white/20 group-hover:bg-bruceInk/10 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
