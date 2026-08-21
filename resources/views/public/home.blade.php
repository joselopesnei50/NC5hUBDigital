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
         O QUE FAZEMOS · Narrativa de Transformação
         ===================================================== --}}

    {{-- Intro --}}
    <section class="relative bg-[#050505] pt-24 lg:pt-32 pb-8 overflow-hidden border-t border-white/5" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="absolute top-0 right-0 w-[40%] h-[60%] bg-[#FF7A1A]/5 blur-[150px] rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl reveal" :class="shown ? 'active' : ''">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-4 block">Marketing que vende</span>
                <h2 class="font-display font-extrabold text-3xl md:text-5xl lg:text-6xl text-white leading-[1.05] mb-6">Pequenas e médias empresas crescem com <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF7A1A] to-[#FFA866]">método</span>, não com sorte.</h2>
                <p class="text-lg md:text-xl text-white/60 leading-relaxed max-w-2xl">Combinamos posicionamento de marca, automação comercial e inteligência artificial para transformar sua presença digital em faturamento real.</p>
            </div>
        </div>
    </section>

    {{-- PILAR 1 · Posicionamento & Conteúdo --}}
    <section class="relative bg-[#050505] py-20 lg:py-28 overflow-hidden" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                {{-- Coluna de texto --}}
                <div class="reveal" :class="shown ? 'active' : ''">
                    <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-1.5 mb-6">
                        <span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-white/60">Pilar 01</span>
                    </div>

                    <h3 class="font-display font-extrabold text-3xl md:text-4xl text-white leading-tight mb-6">Antes de vender, sua marca precisa ser <span class="text-[#FF7A1A]">lembrada</span>.</h3>
                    
                    <p class="text-base md:text-lg text-white/60 leading-relaxed mb-10">O seu cliente compra de quem ele reconhece. Construímos a presença digital da sua empresa com conteúdo estratégico e posicionamento que gera autoridade — para que, quando ele estiver pronto para comprar, <strong class="text-white/80">você seja a escolha óbvia</strong>.</p>

                    <div class="space-y-4">
                        @php
                            $pilar1 = [
                                ['t' => 'Gestão e posicionamento de redes sociais', 'sub' => 'Instagram, Facebook, LinkedIn e TikTok gerenciados com estratégia de crescimento.'],
                                ['t' => 'Criação de conteúdo estratégico', 'sub' => 'Posts, vídeos e copy que conectam com a dor do seu público e geram engajamento real.'],
                                ['t' => 'Identidade visual e branding', 'sub' => 'Marca profissional que transmite confiança e diferencia você da concorrência.'],
                                ['t' => 'Sites e landing pages de conversão', 'sub' => 'Presença digital projetada para transformar visitante em cliente.'],
                            ];
                        @endphp
                        @foreach($pilar1 as $i => $item)
                            <div class="group flex gap-4 p-4 rounded-2xl hover:bg-white/[0.03] transition-colors reveal reveal-delay-{{ $i + 1 }}" :class="shown ? 'active' : ''">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-[#FF7A1A]/10 border border-[#FF7A1A]/20 flex items-center justify-center group-hover:bg-[#FF7A1A]/20 transition-colors">
                                    <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $item['t'] }}</p>
                                    <p class="text-xs text-white/50 mt-1 leading-relaxed">{{ $item['sub'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Coluna visual --}}
                <div class="relative reveal reveal-delay-2" :class="shown ? 'active' : ''">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#FF7A1A]/10 to-blue-900/10 blur-[80px] rounded-full pointer-events-none"></div>
                    <div class="relative glass-card rounded-3xl p-8 md:p-10 border-white/10 hover:border-[#FF7A1A]/30 transition-colors duration-500 overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-[#FF7A1A]/5 rounded-full blur-[60px] pointer-events-none"></div>
                        
                        {{-- Visual abstrato representando marca/conteúdo --}}
                        <div class="space-y-5 relative z-10">
                            <div class="flex items-center gap-4 pb-5 border-b border-white/10">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#FF7A1A] to-[#E5651A] flex items-center justify-center shadow-lg shadow-[#FF7A1A]/20">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4V2m0 2a2 2 0 110 4m0-4a2 2 0 100 4m10-4V2m0 2a2 2 0 110 4m0-4a2 2 0 100 4m-5 6v-2m0 2a2 2 0 110 4m0-4a2 2 0 100 4M3 12a9 9 0 0118 0 9 9 0 01-18 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-sm">Posicionamento de Marca</p>
                                    <p class="text-white/40 text-xs">Estratégia · Conteúdo · Design</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white/5 rounded-2xl p-5 border border-white/5">
                                    <p class="text-3xl font-black text-white mb-1">+340%</p>
                                    <p class="text-xs text-white/50 font-medium">Alcance orgânico médio</p>
                                </div>
                                <div class="bg-white/5 rounded-2xl p-5 border border-white/5">
                                    <p class="text-3xl font-black text-[#FF7A1A] mb-1">5x</p>
                                    <p class="text-xs text-white/50 font-medium">Mais engajamento</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-4 py-3">
                                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                                <p class="text-xs font-bold text-emerald-400">Sua marca visível para quem compra</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PILAR 2 · Vendas & Automação --}}
    <section class="relative bg-[#050505] py-20 lg:py-28 overflow-hidden border-t border-white/5" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="absolute bottom-0 left-0 w-[40%] h-[50%] bg-blue-900/8 blur-[150px] rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                {{-- Coluna visual (esquerda no desktop) --}}
                <div class="order-2 lg:order-1 relative reveal reveal-delay-2" :class="shown ? 'active' : ''">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/10 to-[#FF7A1A]/5 blur-[80px] rounded-full pointer-events-none"></div>
                    <div class="relative glass-card rounded-3xl p-8 md:p-10 border-white/10 hover:border-[#FF7A1A]/30 transition-colors duration-500 overflow-hidden">
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-blue-900/10 rounded-full blur-[60px] pointer-events-none"></div>

                        {{-- Visual abstrato representando funil/vendas --}}
                        <div class="space-y-5 relative z-10">
                            <div class="flex items-center gap-4 pb-5 border-b border-white/10">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-500/20">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-sm">Motor de Vendas</p>
                                    <p class="text-white/40 text-xs">Automação · CRM · Performance</p>
                                </div>
                            </div>

                            {{-- Funil visual simplificado --}}
                            <div class="space-y-3">
                                <div class="bg-white/5 rounded-xl p-4 border border-white/5 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </div>
                                        <span class="text-sm font-medium text-white/80">Visitantes</span>
                                    </div>
                                    <span class="text-xs font-bold text-white/40">Topo</span>
                                </div>
                                <div class="bg-white/5 rounded-xl p-4 border border-white/5 flex items-center justify-between mx-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#FF7A1A]/20 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        </div>
                                        <span class="text-sm font-medium text-white/80">Leads qualificados</span>
                                    </div>
                                    <span class="text-xs font-bold text-[#FF7A1A]/60">Meio</span>
                                </div>
                                <div class="bg-emerald-500/10 rounded-xl p-4 border border-emerald-500/20 flex items-center justify-between mx-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <span class="text-sm font-bold text-emerald-400">Vendas</span>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-400/80">Fundo ✓</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 bg-white/5 border border-white/5 rounded-xl px-4 py-3">
                                <svg class="w-4 h-4 text-[#FF7A1A] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <p class="text-xs font-bold text-white/60">Funil rodando 24h no piloto automático</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Coluna de texto (direita no desktop) --}}
                <div class="order-1 lg:order-2 reveal" :class="shown ? 'active' : ''">
                    <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-1.5 mb-6">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-white/60">Pilar 02</span>
                    </div>

                    <h3 class="font-display font-extrabold text-3xl md:text-4xl text-white leading-tight mb-6">Escale suas vendas <span class="text-[#FF7A1A]">sem multiplicar</span> sua equipe.</h3>
                    
                    <p class="text-base md:text-lg text-white/60 leading-relaxed mb-10">Sua empresa não precisa de mais gente — precisa de <strong class="text-white/80">processos inteligentes</strong>. Montamos uma máquina de vendas que captura leads, nutre pelo WhatsApp e entrega clientes prontos para comprar, funcionando 24 horas por dia.</p>

                    <div class="space-y-4">
                        @php
                            $pilar2 = [
                                ['t' => 'Funis de conversão inteligentes', 'sub' => 'Arquiteturas de venda que guiam o lead do primeiro clique até o fechamento.'],
                                ['t' => 'Automação WhatsApp Business', 'sub' => 'API oficial integrada ao seu funil. Atendimento, nutrição e follow-up automáticos.'],
                                ['t' => 'CRM e gestão de pipeline', 'sub' => 'Visão completa de cada lead. Saiba exatamente onde cada oportunidade de venda está.'],
                                ['t' => 'Tráfego pago de performance', 'sub' => 'Investimento em mídia com foco cirúrgico em retorno. Cada real rastreado.'],
                            ];
                        @endphp
                        @foreach($pilar2 as $i => $item)
                            <div class="group flex gap-4 p-4 rounded-2xl hover:bg-white/[0.03] transition-colors reveal reveal-delay-{{ $i + 1 }}" :class="shown ? 'active' : ''">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $item['t'] }}</p>
                                    <p class="text-xs text-white/50 mt-1 leading-relaxed">{{ $item['sub'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PILAR 3 · BruceIA — Destaque Especial --}}
    <section class="relative bg-[#050505] py-20 lg:py-28 overflow-hidden border-t border-white/5" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden glass-card rounded-[2rem] p-8 sm:p-12 lg:p-16 border border-[#FF7A1A]/20 hover:border-[#FF7A1A]/40 transition-colors duration-700 reveal" :class="shown ? 'active' : ''">
                
                {{-- Efeitos de fundo --}}
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[900px] bg-[radial-gradient(circle,_rgba(255,122,26,0.1)_0%,_transparent_55%)] pointer-events-none"></div>
                <div class="absolute top-0 right-0 w-[300px] h-[300px] bg-[#FF7A1A]/5 blur-[100px] rounded-full pointer-events-none animate-bruce-aura"></div>
                <div class="absolute bottom-0 left-0 w-[200px] h-[200px] bg-blue-900/10 blur-[80px] rounded-full pointer-events-none"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    {{-- Coluna de texto --}}
                    <div>
                        <div class="inline-flex items-center gap-2 bg-[#FF7A1A]/10 border border-[#FF7A1A]/20 rounded-full px-4 py-1.5 mb-6">
                            <span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-widest text-[#FF7A1A]">Pilar 03 · Tecnologia Proprietária</span>
                        </div>

                        <h3 class="font-display font-extrabold text-3xl md:text-4xl lg:text-5xl text-white leading-tight mb-6">Inteligência artificial <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF7A1A] to-[#FFA866]">aplicada ao seu negócio</span>.</h3>

                        <p class="text-base md:text-lg text-white/60 leading-relaxed mb-8">A BruceIA não é um chatbot genérico — é uma <strong class="text-white/80">engine proprietária</strong> que cruza dados reais do seu mercado e aponta exatamente onde sua empresa pode crescer. Diagnóstico, análise de concorrência e automação inteligente em um único ecossistema.</p>

                        <div class="space-y-3 mb-10">
                            @php
                                $pilar3 = [
                                    'Diagnóstico automatizado de marca e presença digital',
                                    'Análise de mercado e concorrência com dados reais',
                                    'Automação inteligente de processos comerciais',
                                    'Relatórios e recomendações de crescimento',
                                ];
                            @endphp
                            @foreach($pilar3 as $i => $item)
                                <div class="flex items-center gap-3 reveal reveal-delay-{{ $i + 1 }}" :class="shown ? 'active' : ''">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#FF7A1A]/20 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-sm font-medium text-white/80">{{ $item }}</span>
                                </div>
                            @endforeach
                        </div>

                        <a href="{{ route('analise.index') }}" class="group inline-flex items-center justify-center gap-3 bg-white text-black hover:bg-[#FF7A1A] hover:text-white px-8 py-4 rounded-2xl text-sm font-bold transition-all shadow-[0_0_30px_rgba(255,255,255,0.1)] hover:shadow-[0_0_40px_rgba(255,122,26,0.5)] transform hover:-translate-y-0.5">
                            Testar BruceIA agora — é gratuito
                            <span class="w-7 h-7 bg-black/5 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                        </a>
                    </div>

                    {{-- Coluna visual — Logo BruceIA --}}
                    <div class="flex items-center justify-center reveal reveal-delay-3" :class="shown ? 'active' : ''">
                        <div class="relative">
                            {{-- Aura pulsante --}}
                            <div class="absolute inset-0 bg-[#FF7A1A] rounded-full blur-[60px] animate-bruce-aura"></div>
                            
                            {{-- Logo grande animado --}}
                            <div class="relative z-10 w-48 h-48 md:w-64 md:h-64 lg:w-72 lg:h-72 flex items-center justify-center">
                                <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA — Inteligência Artificial da NC5" class="w-full h-full object-contain animate-bruce-logo drop-shadow-[0_0_40px_rgba(255,122,26,0.4)]">
                            </div>

                            {{-- Badge flutuante --}}
                            <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-[#0A0A0B] border border-[#FF7A1A]/30 rounded-full px-5 py-2 shadow-lg whitespace-nowrap">
                                <span class="text-xs font-bold text-[#FF7A1A]">⚡ Engine Proprietária NC5</span>
                            </div>
                        </div>
                    </div>
                </div>
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
