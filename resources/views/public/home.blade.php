@extends('layouts.public')

@section('title', 'NC5 Hub Digital — Estratégia, Design & Performance')

@section('content')

    {{-- =====================================================
         HERO · Estética Antigravity
         ===================================================== --}}
    <section class="relative min-h-screen flex items-center overflow-hidden pt-24 pb-20 lg:pt-32 lg:pb-32" x-data="{ shown: false }" x-intersect.once="shown = true">
        <!-- Fundos e Gradientes Antigravity -->
        <div class="absolute inset-0 bg-[#050505] z-0"></div>
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-[#FF7A1A]/10 blur-[150px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-900/10 blur-[150px] rounded-full pointer-events-none"></div>
        
        <!-- Grid overlay suave -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDAuNWg0ME0wLjUgMHY0MCIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiLz48L3N2Zz4=')] z-0 opacity-40"></div>

        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-center">
                
                <!-- COLUNA ESQUERDA -->
                <div class="lg:col-span-7">
                    <h1 class="font-sans font-black text-[15vw] sm:text-[10vw] lg:text-[7rem] leading-[0.9] tracking-tighter text-white mb-8 lg:mb-12">
                        <span class="block reveal" :class="shown ? 'active' : ''">Estratégia.</span>
                        <span class="block reveal reveal-delay-1 text-transparent bg-clip-text bg-gradient-to-r from-[#FF7A1A] to-[#FFA866]" :class="shown ? 'active' : ''">Design.</span>
                        <span class="block reveal reveal-delay-2" :class="shown ? 'active' : ''">Escala.</span>
                    </h1>

                    <p class="text-xl sm:text-2xl text-white/70 leading-relaxed font-normal max-w-xl reveal reveal-delay-3" :class="shown ? 'active' : ''">
                        Alinhamos posicionamento de marca, produção de conteúdo e inteligência artificial em uma esteira única de alta performance.
                    </p>

                    <div class="flex flex-wrap gap-12 mt-12 pt-8 border-t border-white/10 max-w-xl reveal reveal-delay-4" :class="shown ? 'active' : ''">
                        <div>
                            <p class="text-white font-bold text-sm">Integração Oficial</p>
                            <p class="text-white/50 text-xs mt-1">API WhatsApp Business</p>
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">Tecnologia Proprietária</p>
                            <p class="text-white/50 text-xs mt-1">BruceIA Engine</p>
                        </div>
                    </div>
                </div>

                <!-- COLUNA DIREITA · Glassmorphism Card -->
                <div class="lg:col-span-5 relative reveal reveal-delay-4" :class="shown ? 'active' : ''">
                    <!-- Fundo brilhante -->
                    <div class="absolute -inset-1 bg-gradient-to-r from-[#FF7A1A]/40 to-transparent rounded-[2.5rem] blur-xl opacity-50"></div>
                    
                    <div class="glass-card rounded-[2rem] p-8 sm:p-10 w-full lg:max-w-md ml-auto relative z-10 overflow-hidden group hover:border-[#FF7A1A]/40 transition-colors duration-500">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-[#FF7A1A]/10 rounded-full blur-[40px] group-hover:bg-[#FF7A1A]/20 transition-all duration-700"></div>

                        <h3 class="font-sans text-2xl sm:text-3xl font-extrabold text-white leading-tight mb-4 tracking-tight">
                            Descubra onde sua marca perde vendas.
                        </h3>
                        <p class="text-white/60 text-sm mb-8 leading-relaxed font-medium">
                            Nossa IA cruza seus dados reais de mercado e entrega um diagnóstico completo com as alavancas exatas de crescimento.
                        </p>
                        
                        <div class="space-y-0 mb-8">
                            <div class="flex items-center gap-4 border-b border-white/10 py-4">
                                <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-sm font-bold text-white/90">Auditoria de Posicionamento</span>
                            </div>
                            <div class="flex items-center gap-4 border-b border-white/10 py-4">
                                <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-sm font-bold text-white/90">Otimização de Conversão (CRO)</span>
                            </div>
                            <div class="flex items-center gap-4 py-4">
                                <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-sm font-bold text-white/90">Roadmap de Crescimento</span>
                            </div>
                        </div>

                        <a href="{{ route('analise.index') }}" class="w-full flex items-center justify-center gap-3 bg-white text-black hover:bg-[#FF7A1A] hover:text-white px-6 py-4 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-[0_0_20px_rgba(255,122,26,0.4)]">
                            Gerar Diagnóstico Gratuito
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =====================================================
         SERVIÇOS · Bento Box
         ===================================================== --}}
    <section class="relative bg-[#050505] py-24 lg:py-32 overflow-hidden border-t border-white/5" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16 reveal" :class="shown ? 'active' : ''">
                <div class="max-w-2xl">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">O que fazemos</span>
                    <h2 class="font-display font-extrabold text-3xl md:text-5xl text-white leading-tight">Uma esteira completa de marca, mídia e tecnologia.</h2>
                </div>
                <a href="{{ route('servicos') }}" class="inline-flex items-center gap-2 text-sm font-bold text-white/70 hover:text-[#FF7A1A] transition-colors">
                    Ver catálogo completo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            @php
                $servicosList = collect([
                    (object)[
                        'nome' => 'Automação WhatsApp com API Oficial', 
                        'descricao' => 'Escalabilidade e automação no atendimento usando a API Oficial do WhatsApp, chatbots inteligentes e integração direta com seu funil de vendas.',
                        'col_span' => 'md:col-span-2'
                    ],
                    (object)[
                        'nome' => 'Desenvolvimento Web', 
                        'descricao' => 'Criação de websites premium, landing pages de alta performance e plataformas personalizadas com foco absoluto em conversão e velocidade.',
                        'col_span' => 'md:col-span-1'
                    ],
                    (object)[
                        'nome' => 'Desenvolvimento de Marca', 
                        'descricao' => 'Construção de posicionamento estratégico, identidade visual premium e diretrizes que elevam a percepção de valor da sua empresa no mercado.',
                        'col_span' => 'md:col-span-1'
                    ],
                    (object)[
                        'nome' => 'Servidor AWS', 
                        'descricao' => 'Arquitetura em nuvem, hospedagem de alta disponibilidade, segurança e escalabilidade garantida utilizando o ecossistema Amazon Web Services.',
                        'col_span' => 'md:col-span-1'
                    ],
                    (object)[
                        'nome' => 'CRM Customizado', 
                        'descricao' => 'Implantação de CRM inteligente com gestão visual por cards, automação de pipeline de vendas e rastreamento completo da jornada do lead.',
                        'col_span' => 'md:col-span-1'
                    ],
                ]);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($servicosList as $index => $servico)
                    <a href="{{ route('servicos') }}" class="group relative glass-card rounded-[1.25rem] p-8 flex flex-col overflow-hidden hover:bg-white/5 transition-all duration-500 hover:border-[#FF7A1A]/30 reveal {{ $servico->col_span }}" style="transition-delay: {{ $index * 100 }}ms;" :class="shown ? 'active' : ''">
                        <!-- Glow interno no hover -->
                        <div class="absolute inset-0 bg-gradient-to-br from-[#FF7A1A]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

                        <div class="flex items-start justify-between mb-8 relative z-10">
                            <span class="w-10 h-10 rounded-xl bg-white/5 group-hover:bg-[#FF7A1A] text-white/50 group-hover:text-white font-display font-bold flex items-center justify-center text-base transition-colors border border-white/5">
                                0{{ $index + 1 }}
                            </span>
                            <svg class="w-5 h-5 text-white/30 group-hover:text-white group-hover:-translate-y-1 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <h3 class="font-display text-2xl font-extrabold leading-tight mb-3 text-white relative z-10">{{ $servico->nome }}</h3>
                        <p class="text-sm leading-relaxed text-white/60 relative z-10">{{ $servico->descricao }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
         METODOLOGIA
         ===================================================== --}}
    <section id="metodologia" class="relative py-24 lg:py-32 scroll-mt-20 bg-[#050505] border-t border-white/5" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/[0.02] to-transparent pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-2xl mb-16 reveal" :class="shown ? 'active' : ''">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">Metodologia NC5</span>
                <h2 class="font-display font-extrabold text-3xl md:text-5xl text-white leading-tight">Do briefing ao pixel, um único fluxo acelerado.</h2>
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
                    <div class="relative flex flex-col justify-between group glass-card rounded-3xl p-8 hover:-translate-y-2 transition-all duration-500 z-10 hover:border-[#FF7A1A]/30 reveal" style="transition-delay: {{ $index * 150 }}ms;" :class="shown ? 'active' : ''">
                        <div>
                            <div class="flex items-center justify-between">
                                <p class="font-display font-black text-5xl text-white/10 group-hover:text-[#FF7A1A] transition-colors">{{ $step['n'] }}</p>
                                @if(!$loop->last)
                                    <!-- Seta de Conexão Horizontal (Desktop) -->
                                    <div class="hidden md:flex items-center justify-center w-9 h-9 rounded-full bg-white/5 border border-white/10 text-white/30 text-sm group-hover:bg-[#FF7A1A] group-hover:border-[#FF7A1A] group-hover:text-white transition-all transform group-hover:translate-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <h3 class="mt-6 font-extrabold text-lg text-white">{{ $step['t'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-white/60">{{ $step['d'] }}</p>
                        </div>

                        <!-- Indicador de fluxo -->
                        <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between text-[11px] font-extrabold uppercase tracking-wider text-white/40">
                            <span>Fase {{ $step['n'] }}</span>
                            @if(!$loop->last)
                                <span class="text-[#FF7A1A] font-bold flex items-center gap-1">
                                    Próximo passo
                                    <svg class="w-3 h-3 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    <svg class="w-3 h-3 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </span>
                            @else
                                <span class="text-emerald-400 font-bold">✓ Entrega final</span>
                            @endif
                        </div>
                    </div>

                    @if(!$loop->last)
                        <!-- Seta Vertical de Conexão (Mobile) -->
                        <div class="flex md:hidden justify-center my-[-10px] z-20 reveal" style="transition-delay: {{ $index * 150 + 50 }}ms;" :class="shown ? 'active' : ''">
                            <div class="w-8 h-8 rounded-full bg-[#FF7A1A] text-white flex items-center justify-center shadow-[0_0_15px_rgba(255,122,26,0.5)]">
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
    <section class="relative py-24 lg:py-32 bg-[#050505] border-t border-white/5" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16 reveal" :class="shown ? 'active' : ''">
                <div class="max-w-2xl">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">Insights & Artigos</span>
                    <h2 class="font-display font-extrabold text-3xl md:text-5xl text-white leading-tight">Leitura sobre marca, performance e inteligência artificial.</h2>
                </div>
                <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm font-bold text-white/70 hover:text-[#FF7A1A] transition-colors">
                    Ler todos os artigos
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($posts as $index => $post)
                    <a href="{{ route('blog.post', $post->slug) }}" class="group block reveal" style="transition-delay: {{ $index * 150 }}ms;" :class="shown ? 'active' : ''">
                        <div class="aspect-[16/10] rounded-3xl glass-card flex items-center justify-center overflow-hidden mb-5 group-hover:border-[#FF7A1A]/40 transition-all duration-500 relative">
                            <!-- Overlay Hover -->
                            <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <span class="font-display font-black text-5xl text-white/10 group-hover:scale-110 group-hover:text-[#FF7A1A] transition-transform duration-700">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest">{{ $post->created_at->format('d M Y') }}</p>
                        <h3 class="mt-2 font-display font-extrabold text-xl text-white group-hover:text-[#FF7A1A] transition-colors line-clamp-2 leading-tight">{{ $post->titulo }}</h3>
                        <p class="mt-2 text-sm text-white/60 line-clamp-2">{{ Str::limit(strip_tags($post->conteudo), 120) }}</p>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-8 text-white/40">Artigos e estudos de caso em breve.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =====================================================
         CTA BRUCE IA
         ===================================================== --}}
    <section class="relative pb-20 lg:pb-32 bg-[#050505]" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden glass-card rounded-3xl p-8 sm:p-12 border border-[#FF7A1A]/20 reveal" :class="shown ? 'active' : ''">
                
                <!-- Efeito Nebulosa/Glow de IA -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-[radial-gradient(circle,_rgba(255,122,26,0.15)_0%,_transparent_60%)] animate-pulse pointer-events-none mix-blend-screen"></div>

                <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-8 z-10">
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <div class="absolute inset-0 bg-[#FF7A1A] blur-[20px] opacity-40 animate-pulse rounded-full"></div>
                            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-16 h-16 flex-shrink-0 relative z-10 animate-bruce-logo">
                        </div>
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-1 block">Diagnóstico com Inteligência Artificial</span>
                            <h3 class="font-display font-extrabold text-2xl lg:text-4xl text-white leading-tight">Quer ver o Bruce analisando sua marca?</h3>
                            <p class="text-sm text-white/70 mt-2 max-w-lg">Receba um diagnóstico completo em menos de 1 minuto diretamente no portal.</p>
                        </div>
                    </div>

                    <!-- Botão Cor Sólida -->
                    <a href="{{ route('analise.index') }}" class="group inline-flex items-center justify-center gap-3 bg-white text-black hover:bg-[#FF7A1A] hover:text-white px-8 py-4 rounded-2xl text-sm font-bold transition-all shadow-[0_0_30px_rgba(255,255,255,0.1)] hover:shadow-[0_0_40px_rgba(255,122,26,0.5)] flex-shrink-0 whitespace-nowrap transform hover:-translate-y-0.5">
                        Gerar análise gratuita
                        <span class="w-7 h-7 bg-black/5 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
