@extends('layouts.public')

@section('title', 'Serviços & Soluções 360 · NC5 Hub')
@section('meta_description', 'Ecossistema completo de aceleração de vendas: gestão de mídias sociais, automação com IA, desenvolvimento web de alta conversão, tráfego pago e CRM.')

@section('content')
<main class="min-h-screen bg-[#050505] text-white relative overflow-hidden pb-24 selection:bg-[#FF7A1A] selection:text-white">

    {{-- Efeitos de Iluminação / Background Antigravity --}}
    <div class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-[#FF7A1A]/10 blur-[150px] rounded-full pointer-events-none"></div>
    <div class="absolute top-[35%] right-[-10%] w-[500px] h-[500px] bg-blue-600/10 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[20%] w-[500px] h-[500px] bg-[#FF7A1A]/5 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDAuNWg0ME0wLjUgMHY0MCIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiLz48L3N2Zz4=')] z-0 opacity-40 pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10 pt-20 sm:pt-28">

        {{-- HERO SECTION --}}
        <div class="text-center max-w-4xl mx-auto mb-20 lg:mb-28">
            <h1 class="font-display font-black text-4xl sm:text-5xl lg:text-7xl text-white tracking-tight leading-[1.05]">
                Não fazemos apenas posts. Construímos máquinas de venda.
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-white/70 font-normal leading-relaxed max-w-3xl mx-auto">
                Esqueça agências que só entregam métricas de vaidade. A NC5 Hub atua de ponta a ponta na sua operação para garantir autoridade, tráfego qualificado e conversão previsível.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('contato.index') }}" class="inline-flex items-center gap-3 bg-white text-black hover:bg-[#FF7A1A] hover:text-white px-8 py-4 rounded-full font-bold text-sm transition-all shadow-[0_0_25px_rgba(255,255,255,0.15)] hover:shadow-[0_0_35px_rgba(255,122,26,0.4)]">
                    Falar com um estrategista
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('analise.index') }}" class="inline-flex items-center gap-2 bg-white/5 hover:bg-white/10 text-white px-7 py-4 rounded-full font-bold text-sm border border-white/10 hover:border-white/20 transition-all">
                    Diagnóstico com Bruce IA
                </a>
            </div>
        </div>

        {{-- BENTO GRID: ECOSSISTEMA DE SERVIÇOS 360 --}}
        <div class="mb-24 lg:mb-32">
            <div class="flex items-center justify-between mb-10 pb-4 border-b border-white/10">
                <h2 class="text-xl sm:text-2xl font-display font-bold text-white flex items-center gap-3">
                    <span class="w-2.5 h-2.5 bg-[#FF7A1A] rounded-full"></span>
                    Soluções Integradas da NC5 Hub
                </h2>
                <span class="text-xs text-white/50 font-mono uppercase tracking-wider">Metodologia 360º</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                {{-- Card 1: Gestão de Mídias Sociais & Conteúdo (Span 7) --}}
                <div class="md:col-span-7 bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-[#FF7A1A]/40 rounded-3xl p-8 sm:p-10 relative overflow-hidden group transition-all duration-300 hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)] flex flex-col justify-between min-h-[320px]">
                    <div class="absolute -right-16 -top-16 w-56 h-56 bg-[#FF7A1A]/10 rounded-full blur-3xl group-hover:bg-[#FF7A1A]/20 transition-all duration-700 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-[#FF7A1A]/10 border border-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A] mb-6 shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] block mb-2">Presença & Autoridade</span>
                        <h3 class="font-display font-bold text-2xl sm:text-3xl text-white mb-4">Gestão de Mídias Sociais</h3>
                        <p class="text-white/70 text-base leading-relaxed max-w-xl font-normal">
                            Conteúdo que gera desejo e posicionamento de mercado. Transformamos o Instagram, TikTok e LinkedIn da sua marca em vitrines profissionais que constroem comunidade e geram demanda ativa.
                        </p>
                    </div>

                    <div class="relative z-10 pt-6 mt-6 border-t border-white/5 flex flex-wrap items-center gap-3 text-xs font-semibold text-white/60">
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Planejamento Editorial</span>
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Reels & Vídeos Curtos</span>
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Copywriting Persuasivo</span>
                    </div>
                </div>

                {{-- Card 2: Automação Inteligente para WhatsApp (Span 5) --}}
                <div class="md:col-span-5 bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-[#25D366]/40 rounded-3xl p-8 sm:p-10 relative overflow-hidden group transition-all duration-300 hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)] flex flex-col justify-between min-h-[320px]">
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-[#25D366]/10 rounded-full blur-3xl group-hover:bg-[#25D366]/20 transition-all duration-700 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-[#25D366]/10 border border-[#25D366]/20 flex items-center justify-center text-[#25D366] mb-6 shadow-inner">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.587 1.961.947 3.016.947 3.183 0 5.769-2.586 5.77-5.766.001-3.181-2.585-5.734-5.77-5.734zm0-2.172c4.418 0 8 3.582 8 8 0 4.418-3.582 8-8 8-1.42 0-2.756-.372-3.92-1.019l-4.111 1.077 1.096-4.004c-.694-1.196-1.065-2.574-1.065-4.054 0-4.418 3.582-8 8-8z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-[#25D366] block mb-2">Conversão 24/7</span>
                        <h3 class="font-display font-bold text-2xl text-white mb-3">Automação para WhatsApp</h3>
                        <p class="text-white/70 text-sm leading-relaxed font-normal">
                            Atendimento imediato, qualificação de leads e disparo de notificações sem risco de bloqueio, operando diretamente na API Oficial da Meta e fluxos inteligentes.
                        </p>
                    </div>

                    <div class="relative z-10 pt-6 mt-6 border-t border-white/5 flex flex-wrap items-center gap-2 text-xs font-semibold text-white/60">
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Triagem Instantânea</span>
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Zero Perda de Leads</span>
                    </div>
                </div>

                {{-- Card 3: Desenvolvimento Web & E-commerces (Span 5) --}}
                <div class="md:col-span-5 bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-blue-500/40 rounded-3xl p-8 sm:p-10 relative overflow-hidden group transition-all duration-300 hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)] flex flex-col justify-between min-h-[320px]">
                    <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-700 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 mb-6 shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-blue-400 block mb-2">Performance Digital</span>
                        <h3 class="font-display font-bold text-2xl text-white mb-3">Desenvolvimento Web</h3>
                        <p class="text-white/70 text-sm leading-relaxed font-normal">
                            Landing Pages ultrarrápidas, sites institucionais e lojas virtuais construídas para converter visitantes em clientes pagantes com design premium e responsividade total.
                        </p>
                    </div>

                    <div class="relative z-10 pt-6 mt-6 border-t border-white/5 flex flex-wrap items-center gap-2 text-xs font-semibold text-white/60">
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Ultra Rápido</span>
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">SEO Técnico</span>
                    </div>
                </div>

                {{-- Card 4: Tráfego Pago & Performance de Mídia (Span 7) --}}
                <div class="md:col-span-7 bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-[#FF7A1A]/40 rounded-3xl p-8 sm:p-10 relative overflow-hidden group transition-all duration-300 hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)] flex flex-col justify-between min-h-[320px]">
                    <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-[#FF7A1A]/10 rounded-full blur-3xl group-hover:bg-[#FF7A1A]/20 transition-all duration-700 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-[#FF7A1A]/10 border border-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A] mb-6 shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] block mb-2">Escala Previsível</span>
                        <h3 class="font-display font-bold text-2xl sm:text-3xl text-white mb-4">Tráfego Pago & Performance</h3>
                        <p class="text-white/70 text-base leading-relaxed max-w-xl font-normal">
                            Campanhas orientadas a ROI no Meta Ads (Instagram/Facebook) e Google Ads. Segmentação cirúrgica para colocar sua oferta na frente de pessoas prontas para comprar.
                        </p>
                    </div>

                    <div class="relative z-10 pt-6 mt-6 border-t border-white/5 flex flex-wrap items-center gap-3 text-xs font-semibold text-white/60">
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Meta Ads</span>
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Google Ads</span>
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Otimização de ROI</span>
                    </div>
                </div>

                {{-- Card 5: CRM & Funis de Vendas (Span 6) --}}
                <div class="md:col-span-6 bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-purple-500/40 rounded-3xl p-8 sm:p-10 relative overflow-hidden group transition-all duration-300 hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)] flex flex-col justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-6 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-purple-400 block mb-2">Organização Comercial</span>
                        <h3 class="font-display font-bold text-2xl text-white mb-3">Implementação de CRM</h3>
                        <p class="text-white/70 text-sm leading-relaxed font-normal">
                            Estruturação do funil de vendas, acompanhamento de cada negociação em andamento e relatórios claros para sua equipe nunca mais perder um fechamento por falta de follow-up.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/5 text-xs text-white/50">
                        Controle total de pipeline e histórico de leads.
                    </div>
                </div>

                {{-- Card 6: Branding & Identidade Visual (Span 6) --}}
                <div class="md:col-span-6 bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-pink-500/40 rounded-3xl p-8 sm:p-10 relative overflow-hidden group transition-all duration-300 hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)] flex flex-col justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center text-pink-400 mb-6 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-pink-400 block mb-2">Posicionamento Visual</span>
                        <h3 class="font-display font-bold text-2xl text-white mb-3">Criação & Redesign de Marca</h3>
                        <p class="text-white/70 text-sm leading-relaxed font-normal">
                            Logotipos, manuais de marca e materiais visuais refinados que transmitem solidez imediata e permitem que sua empresa cobre o valor justo pelo que entrega.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/5 text-xs text-white/50">
                        Identidade coerente do impresso ao ambiente digital.
                    </div>
                </div>

            </div>
        </div>

        {{-- SERVIÇOS CADASTRADOS NO SISTEMA (SE HOUVER) --}}
        @if(isset($servicos) && $servicos->count() > 0)
            <div class="mb-24 lg:mb-32">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-white/10">
                    <h3 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-3">
                        <span class="w-2.5 h-2.5 bg-[#FF7A1A] rounded-full"></span>
                        Catálogo Ativo de Especialidades
                    </h3>
                    <span class="text-xs text-white/50 font-mono">{{ $servicos->count() }} soluções cadastradas</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($servicos as $servico)
                        <div class="bg-[#0F1729]/60 border border-white/10 rounded-2xl p-6 flex flex-col justify-between hover:border-white/20 transition-all">
                            <div>
                                <h4 class="font-bold text-white text-lg mb-2">{{ $servico->nome }}</h4>
                                <p class="text-sm text-white/60 leading-relaxed font-normal">
                                    {{ $servico->descricao ?: 'Solução personalizada executada sob medida para sua operação.' }}
                                </p>
                            </div>
                            <div class="pt-6 mt-6 border-t border-white/5 flex items-center justify-between">
                                <a href="{{ route('contato.index') }}" class="text-xs font-bold text-[#FF7A1A] hover:text-white transition-colors">
                                    Consultar viabilidade →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- BANNER BRUCE IA & CTA FINAL --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-[#0F1729] via-[#141E33] to-[#0F1729] rounded-3xl p-8 sm:p-12 lg:p-16 border border-[#FF7A1A]/25 shadow-2xl">
            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-[#FF7A1A]/15 blur-[120px] rounded-full pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="w-20 h-20 rounded-2xl bg-white/[0.04] border border-white/10 flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(255,122,26,0.25)]">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="Bruce IA" class="w-12 h-12">
                    </div>
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] block mb-1">Diagnóstico Empresarial Estratégico</span>
                        <h3 class="font-display font-bold text-2xl sm:text-3xl text-white">Pronto para dar o próximo salto com a NC5 Hub?</h3>
                        <p class="text-white/70 text-sm sm:text-base mt-2 max-w-xl">
                            Descubra quais soluções têm o maior impacto no crescimento do seu negócio hoje ou fale diretamente com nossos diretores de estratégia.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full sm:w-auto">
                    <a href="{{ route('contato.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#FF8F3D] text-white px-8 py-4 rounded-full font-bold text-sm transition-all shadow-[0_0_25px_rgba(255,122,26,0.4)] hover:shadow-[0_0_35px_rgba(255,122,26,0.6)]">
                        Solicitar Proposta Sob Medida
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection
