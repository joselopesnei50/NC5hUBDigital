@extends('layouts.public')

@section('title', 'Serviços · NC5 Hub')

@section('content')
    <!-- Header -->
    <section class="relative overflow-hidden bg-ink text-white">
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-bruce/20 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-24 lg:pt-32 lg:pb-40">
            <div class="max-w-3xl">
                <span class="inline-block bg-white/10 border border-white/10 text-white/70 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-8">Soluções NC5</span>
                <h1 class="font-display font-bold text-5xl md:text-7xl leading-[0.95] tracking-tight">
                    O que fazemos<br>pela sua <em class="not-italic text-bruce">marca</em>.
                </h1>
                <p class="mt-8 text-lg md:text-xl text-white/70 max-w-2xl">Cada serviço é projetado para gerar resultado real. Sem fórmulas prontas — estratégia sob medida do briefing ao KPI.</p>
            </div>
        </div>
    </section>

    <!-- Catálogo -->
    <section class="relative py-24 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($servicosList as $index => $servico)
                    <div class="group relative bg-white border border-black/5 rounded-[1.25rem] p-6 flex flex-col overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-ink/5">
                        <div class="flex items-start justify-between mb-5">
                            <span class="font-display font-black text-4xl text-mist group-hover:text-[#FF7A1A] transition-colors">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="w-8 h-8 rounded-full bg-mist group-hover:bg-[#0A1128] group-hover:text-white text-[#0A1128] flex items-center justify-center transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </span>
                        </div>

                        <h3 class="font-display text-xl font-bold text-[#0A1128] mb-2 leading-tight">{{ $servico->nome }}</h3>
                        <p class="text-slate text-[13px] leading-relaxed flex-grow">{{ $servico->descricao }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="relative overflow-hidden bg-ink py-24 lg:py-32">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-bruce/10 rounded-full blur-[160px] pointer-events-none"></div>

        <div class="relative max-w-4xl mx-auto px-4 text-center">
            <img src="{{ asset('images/simbolo.svg') }}" alt="" class="h-14 mx-auto mb-8 brightness-0 invert opacity-30">
            <h2 class="font-display font-bold text-4xl md:text-6xl text-white leading-tight">Pronto para <em class="not-italic text-bruce">escalar</em>?</h2>
            <p class="mt-8 text-lg text-white/70 max-w-xl mx-auto">Cada projeto começa com uma conversa curta. Nos conte onde quer chegar e desenhamos a estratégia.</p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('analise.index') }}" class="inline-flex items-center gap-3 bg-white hover:bg-bruce hover:text-white text-ink px-8 py-4 rounded-full text-base font-bold transition-all shadow-2xl">
                    Diagnóstico gratuito com IA
                </a>
                <a href="{{ route('blog') }}" class="inline-flex items-center gap-3 text-white/70 hover:text-white px-8 py-4 rounded-full text-base font-bold transition-colors">
                    Ver insights
                </a>
            </div>
        </div>
    </section>
@endsection
