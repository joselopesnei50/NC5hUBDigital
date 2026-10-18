@extends('layouts.public')

@section('title', 'Serviços & Soluções 360 · NC5 Hub')
@section('meta_description', 'Ecossistema completo de aceleração de vendas: gestão de mídias sociais, automação com IA, desenvolvimento web de alta conversão, tráfego pago e CRM.')

@section('content')
<main class="min-h-screen bg-[#050505] text-white relative overflow-hidden pb-24 selection:bg-[#FF7A1A] selection:text-white">

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
                <a href="{{ route('contato.index') }}" class="inline-flex items-center gap-3 bg-white text-black hover:bg-white/90 px-8 py-4 rounded-full font-bold text-sm transition-all shadow-md">
                    Falar com um estrategista
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('analise.index') }}" class="inline-flex items-center gap-2 bg-white/5 hover:bg-white/10 text-white px-7 py-4 rounded-full font-bold text-sm border border-white/15 transition-all">
                    Diagnóstico com Bruce IA
                </a>
            </div>
        </div>

        {{-- SEÇÃO MINIMALISTA: SOLUÇÕES INTEGRADAS DA NC5 HUB --}}
        <div class="mb-24 lg:mb-32">
            <div class="flex items-center justify-between mb-10 pb-4 border-b border-white/10">
                <h2 class="text-2xl font-display font-bold text-white tracking-tight">
                    Soluções Integradas da NC5 Hub
                </h2>
                <span class="text-xs text-white/50 font-mono uppercase tracking-wider">Metodologia 360º</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Solução 1: Gestão de Mídias Sociais --}}
                <div class="bg-[#0B0F19] border border-white/10 rounded-2xl p-8 flex flex-col justify-between hover:border-white/25 transition-colors">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-xl text-white mb-3">Gestão de Mídias Sociais</h3>
                        <p class="text-white/65 text-sm leading-relaxed font-normal">
                            Conteúdo que gera desejo e autoridade. Transformamos suas redes numa vitrine profissional que constrói audiência e gera demanda real.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/5 flex items-center justify-between text-xs text-white/50">
                        <span>Instagram • TikTok • LinkedIn</span>
                    </div>
                </div>

                {{-- Solução 2: Automação para WhatsApp --}}
                <div class="bg-[#0B0F19] border border-white/10 rounded-2xl p-8 flex flex-col justify-between hover:border-white/25 transition-colors">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white mb-6">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.587 1.961.947 3.016.947 3.183 0 5.769-2.586 5.77-5.766.001-3.181-2.585-5.734-5.77-5.734zm0-2.172c4.418 0 8 3.582 8 8 0 4.418-3.582 8-8 8-1.42 0-2.756-.372-3.92-1.019l-4.111 1.077 1.096-4.004c-.694-1.196-1.065-2.574-1.065-4.054 0-4.418 3.582-8 8-8z"/>
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-xl text-white mb-3">Automação para WhatsApp</h3>
                        <p class="text-white/65 text-sm leading-relaxed font-normal">
                            Atendimento rápido 24/7, triagem de leads e disparo de mensagens utilizando a API Oficial da Meta sem risco de bloqueio.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/5 flex items-center justify-between text-xs text-white/50">
                        <span>API Oficial • Triagem Instantânea</span>
                    </div>
                </div>

                {{-- Solução 3: Desenvolvimento Web --}}
                <div class="bg-[#0B0F19] border border-white/10 rounded-2xl p-8 flex flex-col justify-between hover:border-white/25 transition-colors">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-xl text-white mb-3">Desenvolvimento Web</h3>
                        <p class="text-white/65 text-sm leading-relaxed font-normal">
                            Landing pages e sites institucionais modernos, rápidos e orientados a conversão, proporcionando a melhor experiência aos seus clientes.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/5 flex items-center justify-between text-xs text-white/50">
                        <span>Landing Pages • E-commerces • SEO</span>
                    </div>
                </div>

                {{-- Solução 4: Tráfego Pago & Performance --}}
                <div class="bg-[#0B0F19] border border-white/10 rounded-2xl p-8 flex flex-col justify-between hover:border-white/25 transition-colors">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-xl text-white mb-3">Tráfego Pago & Mídia</h3>
                        <p class="text-white/65 text-sm leading-relaxed font-normal">
                            Gestão de campanhas de alta performance no Meta Ads e Google Ads com foco estrito em retorno sobre o investimento (ROI).
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/5 flex items-center justify-between text-xs text-white/50">
                        <span>Meta Ads • Google Ads • Escala</span>
                    </div>
                </div>

                {{-- Solução 5: Implementação de CRM --}}
                <div class="bg-[#0B0F19] border border-white/10 rounded-2xl p-8 flex flex-col justify-between hover:border-white/25 transition-colors">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-xl text-white mb-3">Implementação de CRM</h3>
                        <p class="text-white/65 text-sm leading-relaxed font-normal">
                            Organização do funil de vendas e histórico de clientes para garantir que nenhum lead qualificado seja esquecido pela sua equipe.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/5 flex items-center justify-between text-xs text-white/50">
                        <span>Funil de Vendas • Pipeline Comercial</span>
                    </div>
                </div>

                {{-- Solução 6: Criação & Redesign de Marca --}}
                <div class="bg-[#0B0F19] border border-white/10 rounded-2xl p-8 flex flex-col justify-between hover:border-white/25 transition-colors">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-xl text-white mb-3">Criação & Redesign de Marca</h3>
                        <p class="text-white/65 text-sm leading-relaxed font-normal">
                            Identidade visual sólida e materiais de comunicação que posicionam sua empresa como líder no seu segmento de atuação.
                        </p>
                    </div>
                    <div class="pt-6 mt-6 border-t border-white/5 flex items-center justify-between text-xs text-white/50">
                        <span>Identidade Visual • Manuais • Design</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- SERVIÇOS CADASTRADOS NO SISTEMA (SE HOUVER) --}}
        @if(isset($servicos) && $servicos->count() > 0)
            <div class="mb-24 lg:mb-32">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-white/10">
                    <h3 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-3">
                        <span class="w-2 h-2 bg-white rounded-full"></span>
                        Catálogo Ativo de Especialidades
                    </h3>
                    <span class="text-xs text-white/50 font-mono">{{ $servicos->count() }} soluções cadastradas</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($servicos as $servico)
                        <div class="bg-[#0B0F19] border border-white/10 rounded-2xl p-6 flex flex-col justify-between hover:border-white/25 transition-colors">
                            <div>
                                <h4 class="font-bold text-white text-lg mb-2">{{ $servico->nome }}</h4>
                                <p class="text-sm text-white/60 leading-relaxed font-normal">
                                    {{ $servico->descricao ?: 'Solução personalizada executada sob medida para sua operação.' }}
                                </p>
                            </div>
                            <div class="pt-6 mt-6 border-t border-white/5 flex items-center justify-between">
                                <a href="{{ route('contato.index') }}" class="text-xs font-bold text-white hover:text-[#FF7A1A] transition-colors">
                                    Consultar viabilidade →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- BANNER MINIMALISTA BRUCE IA & CTA FINAL --}}
        <div class="bg-[#0B0F19] border border-white/10 rounded-3xl p-8 sm:p-12 lg:p-14">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="Bruce IA" class="w-10 h-10">
                    </div>
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-widest text-white/50 block mb-1">Diagnóstico Estratégico</span>
                        <h3 class="font-display font-bold text-2xl sm:text-3xl text-white">Pronto para acelerar com a NC5 Hub?</h3>
                        <p class="text-white/65 text-sm sm:text-base mt-2 max-w-xl">
                            Descubra onde sua operação tem mais oportunidade de ganho de receita ou fale diretamente com a nossa equipe.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full sm:w-auto">
                    <a href="{{ route('contato.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-white text-black hover:bg-white/90 px-8 py-4 rounded-full font-bold text-sm transition-all shadow-md">
                        Solicitar Proposta Sob Medida
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection
