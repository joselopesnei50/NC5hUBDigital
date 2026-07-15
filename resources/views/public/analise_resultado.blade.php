@extends('layouts.public')

@section('title', 'Diagnóstico gerado · BruceIA')

@section('content')
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-10">
                <span class="inline-block bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold uppercase tracking-[0.25em] px-4 py-1.5 rounded-full">Relatório gerado com sucesso</span>
                <h1 class="mt-6 font-display font-bold text-4xl md:text-5xl text-ink leading-tight tracking-tight">
                    Diagnóstico da sua <em class="not-italic text-bruce">{{ ucfirst(str_replace('_', ' ', $lead->tipo_analise)) }}</em>
                </h1>
                <p class="mt-4 text-slate">Análise realizada para: <span class="font-semibold text-ink break-all">{{ $lead->url_analise }}</span></p>
            </div>

            <article class="bg-white rounded-3xl shadow-2xl shadow-bruceInk/10 overflow-hidden border border-black/5 mb-10">
                <div class="bg-bruceInk p-6 text-white flex items-center gap-4 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-bruce/20 rounded-full blur-[60px] pointer-events-none"></div>
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-12 h-12 flex-shrink-0 relative">
                    <div class="relative">
                        <h2 class="font-display text-xl font-bold">Parecer Estratégico do Bruce</h2>
                        <p class="text-sm text-white/60">Gerado sob a ótica de conversão e marketing premium.</p>
                    </div>
                </div>

                <div class="prose prose-lg max-w-none p-8 md:p-12 prose-headings:font-display prose-headings:text-ink prose-a:text-bruce prose-strong:text-ink text-slate leading-relaxed">
                    {!! $resultado !!}
                </div>

                <div class="px-8 md:px-12 py-5 bg-mist border-t border-black/5 flex items-center justify-between text-xs text-slate">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-bruce rounded-full"></span>
                        Gerado por <strong class="text-ink">BruceIA</strong> · {{ now()->format('d/m/Y H:i') }}
                    </div>
                    <span>NC5 Hub · Ecossistema de performance</span>
                </div>
            </article>

            <div class="relative overflow-hidden bg-bruceInk rounded-3xl p-10 text-center shadow-2xl shadow-bruceInk/30">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-bruce/20 rounded-full blur-[100px] pointer-events-none"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-bruce/10 rounded-full blur-[100px] pointer-events-none"></div>

                <div class="relative">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="" class="w-14 h-14 mx-auto mb-6 opacity-70">
                    <h3 class="font-display text-3xl font-bold text-white mb-4">Gostou das oportunidades?</h3>
                    <p class="text-white/70 mb-8 max-w-xl mx-auto">O Bruce apontou o caminho. A execução requer uma agência especialista — a NC5 Hub está pronta para transformar esses pontos em faturamento real.</p>
                    <a href="#" class="inline-flex items-center gap-3 bg-bruce hover:bg-white hover:text-bruceInk text-white px-8 py-4 rounded-full text-base font-bold transition-all shadow-lg shadow-bruce/30">
                        Falar com um consultor NC5
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
