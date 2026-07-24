@extends('layouts.public')

@section('title', 'Diagnóstico Estratégico · BruceIA')

@section('content')
    <section class="py-24 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-emerald-100 border border-emerald-200 text-emerald-800 text-[10px] font-extrabold uppercase tracking-widest px-4 py-2 rounded-full mb-6">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    Relatório Concluído
                </div>
                <h1 class="font-display font-extrabold text-4xl md:text-5xl text-[#0A1128] leading-tight tracking-tight">
                    Seu Diagnóstico de <em class="not-italic text-[#FF7A1A]">{{ ucfirst(str_replace('_', ' ', $lead->tipo_analise)) }}</em>
                </h1>
                <p class="mt-4 text-slate-500 font-medium">Alvo da análise: <span class="font-bold text-[#0A1128] break-all">{{ $lead->url_analise }}</span></p>
            </div>

            <!-- Card de Resultado -->
            <article class="bg-white rounded-[2rem] shadow-xl border border-slate-200/60 overflow-hidden mb-12">
                
                <!-- Cabeçalho do Relatório -->
                <div class="bg-[#0A1128] p-8 md:p-10 text-white flex items-center gap-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-[#FF7A1A]/10 rounded-full blur-[80px] pointer-events-none translate-x-1/3 -translate-y-1/3"></div>
                    <div class="relative w-16 h-16 flex items-center justify-center flex-shrink-0">
                        <div class="absolute inset-0 bg-[#FF7A1A]/30 rounded-full blur-[15px] animate-bruce-aura"></div>
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-12 h-12 relative z-10 animate-bruce-logo">
                    </div>
                    <div class="relative">
                        <h2 class="font-display text-2xl font-extrabold mb-1">Parecer Estratégico do Bruce</h2>
                        <p class="text-sm font-medium text-white/60">Análise de dados focada na resolução do seu gargalo comercial.</p>
                    </div>
                </div>

                <!-- Conteúdo do Relatório Markdown -->
                <div class="prose prose-lg max-w-none p-8 md:p-12 prose-headings:font-display prose-headings:font-extrabold prose-headings:text-[#0A1128] prose-a:text-[#FF7A1A] prose-strong:text-[#0A1128] prose-strong:font-extrabold text-slate-600 font-medium leading-relaxed prose-li:marker:text-[#FF7A1A]">
                    {!! $resultado !!}
                </div>

                <!-- Rodapé do Card -->
                <div class="px-8 md:px-12 py-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-widest">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-[#FF7A1A] rounded-full"></span>
                        Gerado por <span class="text-[#0A1128]">BruceIA</span>
                    </div>
                    <span>Data: {{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </article>

            <!-- Call to Action Final -->
            <div class="relative overflow-hidden bg-[#0A1128] rounded-[2rem] p-12 text-center shadow-2xl">
                <div class="absolute top-0 left-0 w-80 h-80 bg-[#FF7A1A]/10 rounded-full blur-[100px] pointer-events-none -translate-x-1/3 -translate-y-1/3"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-[#E63888]/10 rounded-full blur-[100px] pointer-events-none translate-x-1/3 translate-y-1/3"></div>

                <div class="relative">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="" class="w-16 h-16 mx-auto mb-6 opacity-80">
                    <h3 class="font-display text-3xl font-extrabold text-white mb-4">Gostou das oportunidades?</h3>
                    <p class="text-white/70 font-medium mb-10 max-w-xl mx-auto leading-relaxed">
                        O Bruce apontou exatamente onde você está perdendo dinheiro. A execução para reverter isso exige uma agência especialista — a NC5 Hub está pronta para agir.
                    </p>
                    <a href="#" class="inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-xl text-sm font-bold transition-all shadow-lg shadow-[#FF7A1A]/20 transform hover:-translate-y-0.5">
                        Falar com um estrategista NC5
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
