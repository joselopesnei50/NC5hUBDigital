@extends('layouts.public')

@section('title', 'O que fazemos · NC5 Hub')

@php
    {{-- TODO: trocar pelo WhatsApp comercial real da NC5 --}}
    $whatsapp = '5511999999999';

    $pacotes = [
        [
            'nome'      => 'Presença Profissional',
            'linha'     => 'O mínimo pra sua marca parecer séria.',
            'publico'   => 'Pra quem ainda não tem site, tem o Instagram bagunçado ou usa WhatsApp pessoal misturado com o do trabalho.',
            'itens'     => [
                'Identidade visual básica (logo, cores, tipografia)',
                'Site institucional de 1 página, focado em conversão',
                'Instagram organizado (bio, destaques, template de post)',
                'WhatsApp Business configurado e separado do pessoal',
                'Google Meu Negócio ativo e otimizado',
            ],
            'destaque'  => false,
            'wa_msg'    => 'Ola! Vi o pacote Presenca Profissional no site da NC5 e quero entender melhor.',
        ],
        [
            'nome'      => 'Gestão 360 de Redes',
            'linha'     => 'Sua presença rodando todo dia — sem você postar.',
            'publico'   => 'Pra quem já vende, quer mais movimento e não tem tempo (nem paciência) pra pensar em conteúdo toda semana.',
            'itens'     => [
                'Planejamento mensal alinhado ao seu calendário comercial',
                'Postagens semanais (feed + reels curtos)',
                'Stories diários com bastidor, oferta e prova social',
                'Resposta de comentários e DMs em horário comercial',
                'Relatório mensal simples: o que funcionou e por quê',
                'Reunião mensal de ajuste com a NC5',
            ],
            'destaque'  => true,
            'wa_msg'    => 'Ola! Vi o pacote Gestao 360 de Redes no site da NC5 e quero entender melhor.',
        ],
        [
            'nome'      => 'Máquina de Vendas',
            'linha'     => 'Cliente entrando todo dia. Sem depender de indicação.',
            'publico'   => 'Pra quem quer previsibilidade — tráfego pago rodando, funil de WhatsApp automatizado e ninguém escapando pelo meio.',
            'itens'     => [
                'Tudo do pacote Gestão 360 de Redes',
                'Tráfego pago (Meta Ads) com verba e criativos',
                'Funil de WhatsApp automatizado com API oficial',
                'CRM básico para acompanhar cada lead',
                'Landing page dedicada às campanhas',
                'Análise mensal com o BruceIA + reunião estratégica',
            ],
            'destaque'  => false,
            'wa_msg'    => 'Ola! Vi o pacote Maquina de Vendas no site da NC5 e quero entender melhor.',
        ],
    ];
@endphp

@section('content')
<div class="bg-[#050505] text-white">

    {{-- HERO --}}
    <section class="pt-28 lg:pt-40 pb-16 lg:pb-24 border-b border-white/10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-display font-extrabold text-white leading-[1.02] tracking-tight
                       text-4xl md:text-6xl lg:text-[76px] mb-8">
                Marketing feito pra <span class="text-[#FF7A1A]">quem vende</span>
                <br class="hidden md:inline"> no dia a dia.
            </h1>
            <p class="text-lg md:text-xl text-white/60 leading-relaxed max-w-2xl mx-auto">
                Somos a operação de marketing do pequeno comerciante e do prestador de serviço.
                Sem enrolação, sem projeto sem fim — três caminhos claros, e você escolhe onde quer começar.
            </p>
        </div>
    </section>

    {{-- PACOTES --}}
    <section class="py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($pacotes as $p)
                    @php
                        $waLink = 'https://wa.me/' . $whatsapp . '?text=' . rawurlencode($p['wa_msg']);
                    @endphp
                    <div class="relative flex flex-col rounded-2xl border
                                {{ $p['destaque'] ? 'border-[#FF7A1A]/60 bg-[#FF7A1A]/[0.04]' : 'border-white/10 bg-white/[0.02]' }}
                                p-8 lg:p-10 transition-colors hover:border-white/25">

                        @if($p['destaque'])
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#FF7A1A] text-white text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full">
                                Mais procurado
                            </span>
                        @endif

                        <h3 class="font-display font-extrabold text-2xl lg:text-[28px] text-white leading-tight tracking-tight mb-3">
                            {{ $p['nome'] }}
                        </h3>

                        <p class="text-sm text-[#FF7A1A] font-semibold mb-5">{{ $p['linha'] }}</p>

                        <p class="text-sm text-white/60 leading-relaxed mb-8">
                            {{ $p['publico'] }}
                        </p>

                        <ul class="space-y-3 mb-10">
                            @foreach($p['itens'] as $item)
                                <li class="flex gap-3 text-sm text-white/85 leading-snug">
                                    <svg class="w-4 h-4 text-[#FF7A1A] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-auto pt-6 border-t border-white/10 space-y-3">
                            <p class="text-xs text-white/40 font-medium">Investimento sob consulta.</p>
                            <a href="{{ $waLink }}" target="_blank" rel="noopener"
                               class="w-full inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold transition-colors
                                      {{ $p['destaque']
                                            ? 'bg-[#FF7A1A] hover:bg-[#E5651A] text-white'
                                            : 'bg-white/10 hover:bg-white text-white hover:text-black' }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                Falar no WhatsApp
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="text-center text-sm text-white/40 mt-12 max-w-2xl mx-auto">
                Não sabe qual escolher? A gente conversa e monta o caminho certo pro seu momento.
                Sem compromisso.
            </p>
        </div>
    </section>

    {{-- CTA final --}}
    <section class="py-24 lg:py-32 border-t border-white/10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-display font-extrabold text-white leading-[1.05] tracking-tight text-3xl md:text-5xl mb-6">
                Ainda em dúvida?
            </h2>
            <p class="text-lg text-white/60 leading-relaxed mb-10 max-w-xl mx-auto">
                Faça uma análise gratuita do seu Instagram ou do seu site.
                Em menos de 1 minuto o BruceIA aponta onde você está perdendo cliente.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('analise.index') }}"
                   class="inline-flex items-center justify-center gap-2 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-2xl text-sm font-bold transition-colors">
                    Fazer análise gratuita
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Ola! Vim pelo site da NC5 e quero conversar sobre marketing.') }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 border border-white/20 hover:border-white/50 text-white px-8 py-4 rounded-2xl text-sm font-bold transition-colors">
                    Falar direto no WhatsApp
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
