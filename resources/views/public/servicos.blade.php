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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($servicos as $index => $servico)
                    <div class="group relative bg-white border border-black/5 rounded-3xl p-8 flex flex-col overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-ink/5">
                        <div class="flex items-start justify-between mb-8">
                            <span class="font-display font-black text-5xl text-mist group-hover:text-bruce transition-colors">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="w-10 h-10 rounded-full bg-mist group-hover:bg-ink group-hover:text-white text-ink flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </span>
                        </div>

                        <h3 class="font-display text-2xl font-bold text-ink mb-3 leading-tight">{{ $servico->nome }}</h3>
                        <p class="text-slate text-sm leading-relaxed flex-grow">{{ $servico->descricao }}</p>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <p class="text-lg text-slate">Nenhum serviço cadastrado. Assim que o catálogo for publicado ele aparece aqui.</p>
                    </div>
                @endforelse
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
