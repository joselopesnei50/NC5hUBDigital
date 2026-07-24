@extends('layouts.public')

@section('title', 'NC5 Hub Digital — Estratégia, Design & Performance')

@section('content')

    {{-- =====================================================
         HERO · Banner Bruce fullscreen · motor de mudança
         ===================================================== --}}
    <section class="relative min-h-[calc(100vh-4rem)] bg-ink overflow-hidden flex items-center">

        <!-- glows sutis de fundo -->
        <div class="absolute -top-40 right-1/4 w-[700px] h-[500px] bg-bruce/15 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-magenta/10 rounded-full blur-[140px] pointer-events-none"></div>

        <!-- grade sutil -->
        <div class="absolute inset-0 opacity-[0.05] pointer-events-none"
             style="background-image: linear-gradient(#FF7A1A 1px, transparent 1px), linear-gradient(90deg, #FF7A1A 1px, transparent 1px); background-size: 56px 56px;"></div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <!-- COLUNA ESQUERDA · texto -->
                <div class="lg:col-span-7 text-white space-y-6">
                    <div class="inline-flex items-center gap-2 bg-white/10 border border-white/15 px-4 py-1.5 rounded-full text-xs font-bold text-bruce uppercase tracking-widest backdrop-blur-sm">
                        ⚡ Inteligência de Produção & Performance
                    </div>

                    <h1 class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl leading-[1.05] tracking-tight text-white">
                        <em class="not-italic text-bruce">IA</em> e <em class="not-italic text-magenta">design</em> de alta performance para acelerar marcas.
                    </h1>

                    <p class="text-base lg:text-lg text-white/75 leading-relaxed max-w-xl">
                        Conheça o <strong class="text-white">Bruce</strong>, o motor de inteligência estratégica da NC5 Hub Digital. Design, tráfego pago e produção integrada na mesma esteira acelerada.
                    </p>

                    <!-- Botões com Cores Sólidas (Sem Degradê) -->
                    <div class="pt-4 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('servicos') }}" class="group inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-7 py-4 rounded-2xl text-base font-bold transition-all shadow-xl shadow-[#FF7A1A]/20 transform hover:-translate-y-0.5">
                            Ver o que fazemos
                            <span class="w-7 h-7 bg-white/20 group-hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </a>

                        <a href="#metodologia" class="inline-flex items-center justify-center gap-3 text-white/90 hover:text-white border border-white/20 hover:border-white/40 bg-white/5 hover:bg-white/10 px-7 py-4 rounded-2xl text-base font-bold transition-all backdrop-blur-sm">
                            Como o Bruce funciona
                        </a>
                    </div>

                    <!-- Tags de Prova / Recursos -->
                    <div class="pt-8 border-t border-white/10 flex flex-wrap gap-3">
                        <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 px-4 py-2 rounded-xl text-xs font-bold text-white/90">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Integração via API Oficial WhatsApp
                        </div>
                        <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 px-4 py-2 rounded-xl text-xs font-bold text-white/90">
                            <span class="w-2 h-2 rounded-full bg-magenta"></span> Esteira Restrita de Aprovação
                        </div>
                    </div>
                </div>

                <!-- COLUNA DIREITA · Ícone Bruce com Halo Sutil -->
                <div class="lg:col-span-5 relative flex items-center justify-center min-h-[380px] lg:min-h-[550px]">
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-[320px] h-[320px] lg:w-[450px] lg:h-[450px] bg-bruce/20 rounded-full blur-[110px] animate-pulse"></div>
                    </div>

                    <div class="relative flex justify-center items-center">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA NC5" class="w-64 h-64 lg:w-80 lg:h-80 xl:w-96 xl:h-96 animate-float relative z-10 filter drop-shadow-[0_10px_40px_rgba(255,122,26,0.3)]">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
         SERVIÇOS
         ===================================================== --}}
    <section class="relative bg-white py-24 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
                <div class="max-w-2xl">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">O que fazemos</span>
                    <h2 class="font-display font-extrabold text-3xl md:text-5xl text-ink leading-tight">Uma esteira completa de marca, mídia e tecnologia.</h2>
                </div>
                <a href="{{ route('servicos') }}" class="inline-flex items-center gap-2 text-sm font-bold text-ink hover:text-[#FF7A1A] transition-colors">
                    Ver catálogo completo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($servicos as $index => $servico)
                    <a href="{{ route('servicos') }}" class="group relative bg-mist border border-slate-200/80 rounded-3xl p-8 flex flex-col overflow-hidden hover:bg-ink hover:text-white transition-all duration-500 hover:shadow-2xl">
                        <div class="flex items-start justify-between mb-8">
                            <span class="w-12 h-12 rounded-2xl bg-white group-hover:bg-[#FF7A1A] group-hover:text-white text-ink font-display font-bold flex items-center justify-center text-lg shadow-sm transition-colors">
                                0{{ $index + 1 }}
                            </span>
                            <svg class="w-5 h-5 text-slate group-hover:text-white group-hover:-translate-y-1 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <h3 class="font-display text-2xl font-extrabold leading-tight mb-3">{{ $servico->nome }}</h3>
                        <p class="text-sm leading-relaxed opacity-75 line-clamp-3">{{ $servico->descricao }}</p>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-16 text-slate">
                        <p class="text-base font-semibold text-ink">Catálogo de Serviços NC5 Hub Digital</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =====================================================
         METODOLOGIA
         ===================================================== --}}
    <section id="metodologia" class="relative py-24 lg:py-32 scroll-mt-20 bg-mist">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-16">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">Metodologia NC5</span>
                <h2 class="font-display font-extrabold text-3xl md:text-5xl text-ink leading-tight">Do briefing ao pixel, um único fluxo acelerado.</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach([
                    ['n' => '01', 't' => 'Diagnóstico', 'd' => 'Auditoria de marca, funil e mercado. Encontramos a alavanca de crescimento.'],
                    ['n' => '02', 't' => 'Estratégia', 'd' => 'Posicionamento, oferta, criativos e canais desenhados sob medida.'],
                    ['n' => '03', 't' => 'Execução', 'd' => 'Design, tecnologia e mídia rodando na mesma cadência de alta produção.'],
                    ['n' => '04', 't' => 'Escala', 'd' => 'Otimização contínua guiada por dashboards e esteira de testes.'],
                ] as $step)
                    <div class="group bg-white border border-slate-200/80 rounded-3xl p-8 hover:-translate-y-2 hover:shadow-xl transition-all duration-500">
                        <p class="font-display font-black text-5xl text-slate-300 group-hover:text-[#FF7A1A] transition-colors">{{ $step['n'] }}</p>
                        <h3 class="mt-6 font-extrabold text-lg text-ink">{{ $step['t'] }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600 font-medium">{{ $step['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         INSIGHTS / BLOG
         ===================================================== --}}
    <section class="relative py-24 lg:py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
                <div class="max-w-2xl">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">Insights & Artigos</span>
                    <h2 class="font-display font-extrabold text-3xl md:text-5xl text-ink leading-tight">Leitura sobre marca, performance e inteligência artificial.</h2>
                </div>
                <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm font-bold text-ink hover:text-[#FF7A1A] transition-colors">
                    Ler todos os artigos
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    <a href="{{ route('blog.post', $post->slug) }}" class="group block">
                        <div class="aspect-[16/10] rounded-3xl bg-mist border border-slate-200/80 flex items-center justify-center overflow-hidden mb-5 group-hover:shadow-xl transition-all">
                            <span class="font-display font-black text-5xl text-slate-300 group-hover:scale-110 group-hover:text-[#FF7A1A] transition-all">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest">{{ $post->created_at->format('d M Y') }}</p>
                        <h3 class="mt-2 font-display font-extrabold text-xl text-ink group-hover:text-[#FF7A1A] transition-colors line-clamp-2 leading-tight">{{ $post->titulo }}</h3>
                        <p class="mt-2 text-xs text-slate-600 line-clamp-2 font-medium">{{ Str::limit(strip_tags($post->conteudo), 120) }}</p>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate">Artigos e estudos de caso em breve.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =====================================================
         CTA BRUCE IA
         ===================================================== --}}
    <section class="relative pb-20 lg:pb-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden bg-ink rounded-3xl p-8 sm:p-12 shadow-2xl">
                <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-16 h-16 flex-shrink-0">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-1 block">Diagnóstico com Inteligência Artificial</span>
                            <h3 class="font-display font-extrabold text-2xl lg:text-4xl text-white leading-tight">Quer ver o Bruce analisando sua marca?</h3>
                            <p class="text-sm text-white/70 mt-2 max-w-lg">Receba um diagnóstico completo em menos de 1 minuto diretamente no portal.</p>
                        </div>
                    </div>

                    <!-- Botão Cor Sólida (Sem Degradê) -->
                    <a href="{{ route('analise.index') }}" class="group inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-2xl text-sm font-bold transition-all shadow-xl shadow-[#FF7A1A]/20 flex-shrink-0 whitespace-nowrap transform hover:-translate-y-0.5">
                        Gerar análise gratuita
                        <span class="w-7 h-7 bg-white/20 group-hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
