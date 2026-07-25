@extends('layouts.public')

@section('title', 'NC5 Hub Digital — Estratégia, Design & Performance')

@section('content')

    {{-- =====================================================
         HERO · Primeiro Bloco Minimalista & Moderno
         ===================================================== --}}
    <section class="relative min-h-[calc(100vh-4rem)] bg-[#0A1128] text-white overflow-hidden flex items-center">

        <!-- Luzes sutis de ambiente (Sem grade) -->
        <div class="absolute -top-32 right-1/4 w-[600px] h-[450px] bg-[#FF7A1A]/10 rounded-full blur-[160px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-[500px] h-[450px] bg-[#FF7A1A]/10 rounded-full blur-[160px] pointer-events-none"></div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <!-- COLUNA ESQUERDA · Conteúdo Principal Minimalista -->
                <div class="lg:col-span-7 space-y-8">
                    
                    <h1 class="font-display font-extrabold text-4xl sm:text-5xl lg:text-7xl leading-[1.04] tracking-tight text-white">
                        Estratégia, <span class="text-[#FF7A1A]">design</span> e inteligência para escalar marcas.
                    </h1>

                    <p class="text-base sm:text-lg text-white/70 leading-relaxed max-w-xl font-normal">
                        Conheça o <strong class="text-white font-semibold">BruceIA</strong>. Alinhamos posicionamento de marca, produção de conteúdo e mídia paga em uma esteira única de alta performance.
                    </p>

                    <!-- Botões com Cores Sólidas (Sem Degradê) -->
                    <div class="pt-2 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('servicos') }}" class="group inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-2xl text-base font-bold transition-all shadow-lg shadow-[#FF7A1A]/20 transform hover:-translate-y-0.5">
                            Ver o que fazemos
                            <span class="w-7 h-7 bg-white/20 group-hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </a>

                        <a href="#metodologia" class="inline-flex items-center justify-center gap-3 text-white/90 hover:text-white border border-white/20 hover:border-white/40 bg-white/5 hover:bg-white/10 px-8 py-4 rounded-2xl text-base font-bold transition-all backdrop-blur-sm">
                            Como o Bruce funciona
                        </a>
                    </div>

                    <!-- Recursos / Prova Sólida -->
                    <div class="pt-8 border-t border-white/10 flex flex-wrap gap-4 text-xs font-semibold text-white/80">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> API Oficial WhatsApp
                        </div>
                        <span class="text-white/20">•</span>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#FF7A1A]"></span> Esteira de Aprovação Restrita
                        </div>
                        <span class="text-white/20">•</span>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#FF7A1A]"></span> BruceIA Motor de Análise
                        </div>
                    </div>
                </div>

                <!-- COLUNA DIREITA · Logo Animado Bruce IA -->
                <div class="lg:col-span-5 relative flex items-center justify-center min-h-[380px] lg:min-h-[520px]">
                    <!-- Aura Animada atrás do Logo -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-[300px] h-[300px] lg:w-[420px] lg:h-[420px] bg-[#FF7A1A]/30 rounded-full animate-bruce-aura"></div>
                    </div>

                    <!-- Logo Bruce IA Animado -->
                    <div class="relative flex justify-center items-center">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA NC5" class="w-64 h-64 lg:w-80 lg:h-80 xl:w-96 xl:h-96 animate-bruce-logo relative z-10 filter drop-shadow-[0_15px_50px_rgba(255,122,26,0.35)]">
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
                    <h2 class="font-display font-extrabold text-3xl md:text-5xl text-[#0A1128] leading-tight">Uma esteira completa de marca, mídia e tecnologia.</h2>
                </div>
                <a href="{{ route('servicos') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#0A1128] hover:text-[#FF7A1A] transition-colors">
                    Ver catálogo completo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            @php
                $servicosList = collect([
                    (object)[
                        'nome' => 'Automação WhatsApp com API Oficial', 
                        'descricao' => 'Escalabilidade e automação no atendimento usando a API Oficial do WhatsApp, chatbots inteligentes e integração direta com seu funil de vendas.'
                    ],
                    (object)[
                        'nome' => 'Desenvolvimento Web', 
                        'descricao' => 'Criação de websites premium, landing pages de alta performance e plataformas personalizadas com foco absoluto em conversão e velocidade.'
                    ],
                    (object)[
                        'nome' => 'Desenvolvimento de Marca', 
                        'descricao' => 'Construção de posicionamento estratégico, identidade visual premium e diretrizes que elevam a percepção de valor da sua empresa no mercado.'
                    ],
                    (object)[
                        'nome' => 'Servidor AWS', 
                        'descricao' => 'Arquitetura em nuvem, hospedagem de alta disponibilidade, segurança e escalabilidade garantida utilizando o ecossistema Amazon Web Services.'
                    ],
                    (object)[
                        'nome' => 'CRM Customizado', 
                        'descricao' => 'Implantação de CRM inteligente com gestão visual por cards, automação de pipeline de vendas e rastreamento completo da jornada do lead.'
                    ],
                ]);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($servicosList as $index => $servico)
                    <a href="{{ route('servicos') }}" class="group relative bg-[#F8FAFC] border border-slate-200/80 rounded-3xl p-8 flex flex-col overflow-hidden hover:bg-[#0A1128] hover:text-white transition-all duration-500 hover:shadow-xl">
                        <div class="flex items-start justify-between mb-8">
                            <span class="w-12 h-12 rounded-2xl bg-white group-hover:bg-[#FF7A1A] group-hover:text-white text-[#0A1128] font-display font-bold flex items-center justify-center text-lg shadow-sm transition-colors">
                                0{{ $index + 1 }}
                            </span>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white group-hover:-translate-y-1 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <h3 class="font-display text-2xl font-extrabold leading-tight mb-3">{{ $servico->nome }}</h3>
                        <p class="text-sm leading-relaxed opacity-75 line-clamp-3">{{ $servico->descricao }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         METODOLOGIA
         ===================================================== --}}
    <section id="metodologia" class="relative py-24 lg:py-32 scroll-mt-20 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-16">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">Metodologia NC5</span>
                <h2 class="font-display font-extrabold text-3xl md:text-5xl text-[#0A1128] leading-tight">Do briefing ao pixel, um único fluxo acelerado.</h2>
            </div>

            <!-- Mapa Mental de Conexão com Setas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                @php
                    $steps = [
                        ['n' => '01', 't' => 'Diagnóstico', 'd' => 'Auditoria de marca, funil e mercado. Encontramos a alavanca de crescimento.'],
                        ['n' => '02', 't' => 'Estratégia', 'd' => 'Posicionamento, oferta, criativos e canais desenhados sob medida.'],
                        ['n' => '03', 't' => 'Execução', 'd' => 'Design, tecnologia e mídia rodando na mesma cadência de alta produção.'],
                        ['n' => '04', 't' => 'Escala', 'd' => 'Otimização contínua guiada por dashboards e esteira de testes.'],
                    ];
                @endphp

                @foreach($steps as $index => $step)
                    <div class="relative flex flex-col justify-between group bg-white border border-slate-200/80 rounded-3xl p-8 hover:-translate-y-2 hover:shadow-xl transition-all duration-500 z-10">
                        <div>
                            <div class="flex items-center justify-between">
                                <p class="font-display font-black text-5xl text-slate-300 group-hover:text-[#FF7A1A] transition-colors">{{ $step['n'] }}</p>
                                @if(!$loop->last)
                                    <!-- Seta de Conexão Horizontal (Desktop) -->
                                    <div class="hidden md:flex items-center justify-center w-9 h-9 rounded-full bg-[#F8FAFC] border border-slate-200 text-[#FF7A1A] font-bold text-sm shadow-sm group-hover:bg-[#FF7A1A] group-hover:text-white transition-all transform group-hover:translate-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <h3 class="mt-6 font-extrabold text-lg text-[#0A1128]">{{ $step['t'] }}</h3>
                            <p class="mt-2 text-xs leading-relaxed text-slate-600 font-medium">{{ $step['d'] }}</p>
                        </div>

                        <!-- Indicador de fluxo / Mapa mental -->
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                            <span>Fase {{ $step['n'] }}</span>
                            @if(!$loop->last)
                                <span class="text-[#FF7A1A] font-bold flex items-center gap-1">
                                    Próximo passo
                                    <svg class="w-3 h-3 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    <svg class="w-3 h-3 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </span>
                            @else
                                <span class="text-emerald-600 font-bold">✓ Entrega final</span>
                            @endif
                        </div>
                    </div>

                    @if(!$loop->last)
                        <!-- Seta Vertical de Conexão (Mobile) -->
                        <div class="flex md:hidden justify-center my-[-10px] z-20">
                            <div class="w-8 h-8 rounded-full bg-[#FF7A1A] text-white flex items-center justify-center shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </div>
                        </div>
                    @endif
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
                    <h2 class="font-display font-extrabold text-3xl md:text-5xl text-[#0A1128] leading-tight">Leitura sobre marca, performance e inteligência artificial.</h2>
                </div>
                <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#0A1128] hover:text-[#FF7A1A] transition-colors">
                    Ler todos os artigos
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    <a href="{{ route('blog.post', $post->slug) }}" class="group block">
                        <div class="aspect-[16/10] rounded-3xl bg-[#F8FAFC] border border-slate-200/80 flex items-center justify-center overflow-hidden mb-5 group-hover:shadow-xl transition-all">
                            <span class="font-display font-black text-5xl text-slate-300 group-hover:scale-110 group-hover:text-[#FF7A1A] transition-all">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest">{{ $post->created_at->format('d M Y') }}</p>
                        <h3 class="mt-2 font-display font-extrabold text-xl text-[#0A1128] group-hover:text-[#FF7A1A] transition-colors line-clamp-2 leading-tight">{{ $post->titulo }}</h3>
                        <p class="mt-2 text-xs text-slate-600 line-clamp-2 font-medium">{{ Str::limit(strip_tags($post->conteudo), 120) }}</p>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-500">Artigos e estudos de caso em breve.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =====================================================
         CTA BRUCE IA
         ===================================================== --}}
    <section class="relative pb-20 lg:pb-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden bg-[#0A1128] rounded-3xl p-8 sm:p-12 shadow-2xl">
                <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-16 h-16 flex-shrink-0 animate-bruce-logo">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-1 block">Diagnóstico com Inteligência Artificial</span>
                            <h3 class="font-display font-extrabold text-2xl lg:text-4xl text-white leading-tight">Quer ver o Bruce analisando sua marca?</h3>
                            <p class="text-sm text-white/70 mt-2 max-w-lg">Receba um diagnóstico completo em menos de 1 minuto diretamente no portal.</p>
                        </div>
                    </div>

                    <!-- Botão Cor Sólida (Sem Degradê) -->
                    <a href="{{ route('analise.index') }}" class="group inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-2xl text-sm font-bold transition-all shadow-lg shadow-[#FF7A1A]/20 flex-shrink-0 whitespace-nowrap transform hover:-translate-y-0.5">
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
