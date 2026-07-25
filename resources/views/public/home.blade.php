@extends('layouts.public')

@section('title', 'NC5 Hub Digital — Estratégia, Design & Performance')

@section('content')

    {{-- =====================================================
         HERO · Primeiro Bloco Minimalista & Moderno
         ===================================================== --}}
    <section class="relative min-h-screen bg-[#0A1128] text-white flex items-center overflow-hidden pt-24 pb-20 lg:pt-32 lg:pb-32">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-center">
                
                <!-- COLUNA ESQUERDA · Tipografia Massiva (Ref: Nixtio / Let's Talk) -->
                <div class="lg:col-span-7">
                    <h1 class="font-sans font-black text-[15vw] sm:text-[10vw] lg:text-[7rem] leading-[0.9] tracking-tighter text-white mb-8 lg:mb-12">
                        Estratégia.<br>
                        <span class="text-[#FF7A1A]">Design.</span><br>
                        Escala.
                    </h1>

                    <p class="text-xl sm:text-2xl text-white/70 leading-relaxed font-normal max-w-xl">
                        Alinhamos posicionamento de marca, produção de conteúdo e inteligência artificial em uma esteira única de alta performance.
                    </p>

                    <div class="flex flex-wrap gap-12 mt-12 pt-8 border-t border-white/10 max-w-xl">
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

                <!-- COLUNA DIREITA · Card Clean Branco (Ref: Let's Talk) -->
                <div class="lg:col-span-5 relative">
                    <!-- Fundo sutil para destacar o card branco -->
                    <div class="absolute -inset-4 bg-[#FF7A1A]/5 rounded-[2.5rem] blur-xl"></div>
                    
                    <div class="bg-white rounded-[2rem] p-8 sm:p-10 w-full lg:max-w-md ml-auto shadow-2xl relative z-10">
                        <h3 class="font-sans text-3xl font-extrabold text-[#0A1128] leading-tight mb-4 tracking-tight">
                            Descubra onde sua marca perde vendas.
                        </h3>
                        <p class="text-slate-500 text-sm mb-8 leading-relaxed font-medium">
                            Nossa IA cruza seus dados reais de mercado e entrega um diagnóstico completo com as alavancas exatas de crescimento.
                        </p>
                        
                        <div class="space-y-0 mb-8">
                            <div class="flex items-center gap-4 border-b border-slate-100 py-4">
                                <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-sm font-bold text-[#0A1128]">Auditoria de Posicionamento</span>
                            </div>
                            <div class="flex items-center gap-4 border-b border-slate-100 py-4">
                                <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-sm font-bold text-[#0A1128]">Otimização de Conversão (CRO)</span>
                            </div>
                            <div class="flex items-center gap-4 py-4">
                                <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-sm font-bold text-[#0A1128]">Roadmap de Crescimento</span>
                            </div>
                        </div>

                        <a href="{{ route('analise.index') }}" class="w-full flex items-center justify-center gap-3 bg-[#0A1128] hover:bg-black text-white px-6 py-4 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Gerar Diagnóstico Gratuito
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($servicosList as $index => $servico)
                    <a href="{{ route('servicos') }}" class="group relative bg-[#F8FAFC] border border-slate-200/80 rounded-[1.25rem] p-6 flex flex-col overflow-hidden hover:bg-[#0A1128] hover:text-white transition-all duration-500 hover:shadow-xl">
                        <div class="flex items-start justify-between mb-5">
                            <span class="w-10 h-10 rounded-xl bg-white group-hover:bg-[#FF7A1A] group-hover:text-white text-[#0A1128] font-display font-bold flex items-center justify-center text-base shadow-sm transition-colors">
                                0{{ $index + 1 }}
                            </span>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-white group-hover:-translate-y-1 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <h3 class="font-display text-xl font-extrabold leading-tight mb-2">{{ $servico->nome }}</h3>
                        <p class="text-[13px] leading-relaxed opacity-75 line-clamp-3">{{ $servico->descricao }}</p>
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
