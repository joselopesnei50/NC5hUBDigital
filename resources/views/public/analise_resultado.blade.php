@extends('layouts.public')

@section('title', 'Resultado da Análise - NC5 Hub')

@push('styles')
<style>
    /* Prose do parecer da IA — minimalista */
    .bruce-prose {
        max-width: none;
        color: rgba(255, 255, 255, 0.72);
        font-family: 'Inter', sans-serif;
        line-height: 1.75;
        font-size: 1rem;
    }
    .bruce-prose > *:first-child { margin-top: 0; }
    .bruce-prose h2,
    .bruce-prose h3 {
        font-family: 'Fraunces', serif;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 1.5rem;
        line-height: 1.25;
        letter-spacing: -0.015em;
        margin-top: 3rem;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .bruce-prose h4 {
        font-family: 'Fraunces', serif;
        color: #FFFFFF;
        font-weight: 600;
        font-size: 1.125rem;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
    }
    .bruce-prose p { margin-bottom: 1.25rem; }
    .bruce-prose ul,
    .bruce-prose ol {
        padding-left: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .bruce-prose ul { list-style-type: none; padding-left: 1.5rem; }
    .bruce-prose ul li { position: relative; margin-bottom: 0.6rem; }
    .bruce-prose ul li::before {
        content: "";
        position: absolute;
        left: -1rem;
        top: 0.75rem;
        width: 0.375rem;
        height: 1px;
        background: #FF7A1A;
    }
    .bruce-prose ol { list-style-position: outside; }
    .bruce-prose ol li { margin-bottom: 0.6rem; padding-left: 0.25rem; }
    .bruce-prose ol li::marker { color: #FF7A1A; font-weight: 700; }
    .bruce-prose strong { color: #FFFFFF; font-weight: 600; }
    .bruce-prose a { color: #FF7A1A; text-decoration: underline; text-underline-offset: 3px; }
    .bruce-prose a:hover { color: #FFFFFF; }
    .bruce-prose blockquote {
        border-left: 2px solid #FF7A1A;
        padding-left: 1.25rem;
        margin: 1.5rem 0;
        color: rgba(255,255,255,0.85);
        font-style: normal;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#050505] font-sans text-white pb-24">

    {{-- Header --}}
    <section class="pt-24 lg:pt-32 pb-10 lg:pb-14 border-b border-white/10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-widest text-emerald-400 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Relatório concluído
            </div>

            <h1 class="font-display font-extrabold text-white leading-[1.05] tracking-tight text-3xl md:text-5xl lg:text-6xl mb-6">
                Análise de <span class="text-[#FF7A1A]">{{ $lead->tipo_analise ?? 'projeto' }}</span>
            </h1>

            @if(!empty($lead->url_analise))
                <a href="{{ $lead->url_analise }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 text-sm text-white/60 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.828 10.172a4 4 0 015.656 0l1.414 1.414a4 4 0 010 5.656l-2.828 2.828a4 4 0 01-5.656 0l-1.414-1.414M10.172 13.828a4 4 0 01-5.656 0L3.1 12.414a4 4 0 010-5.656L5.929 3.93a4 4 0 015.656 0l1.414 1.414"/></svg>
                    {{ $lead->url_analise }}
                </a>
            @endif

            <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-white/50">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-white/80 font-medium">{{ $lead->nome ?? 'Cliente' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ isset($lead->created_at) ? $lead->created_at->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">

        {{-- Auditoria técnica (site) --}}
        @if(isset($lead->tipo_analise) && $lead->tipo_analise === 'site' && isset($lead->seo_score))
            <section class="mb-16">
                <div class="flex items-baseline justify-between mb-8 pb-4 border-b border-white/10">
                    <h2 class="font-display font-extrabold text-xl md:text-2xl text-white tracking-tight">Auditoria técnica</h2>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-white/40">Lighthouse</span>
                </div>

                @if(isset($lead->lcp_time) && $lead->lcp_time > 0)
                    <div class="mb-8 flex items-center gap-3 text-sm">
                        <span class="text-white/50">Tempo de carregamento (LCP):</span>
                        <span class="font-bold {{ $lead->lcp_time > 2.5 ? 'text-red-400' : 'text-emerald-400' }}">{{ $lead->lcp_time }}s</span>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4"
                     x-data="{ currentSc: 0, currentPerf: 0, currentMob: 0, currentBp: 0 }"
                     x-init="
                        setTimeout(() => {
                            let sc = setInterval(() => { if (currentSc < {{ $lead->seo_score }}) currentSc++; else clearInterval(sc); }, 20);
                            let perf = setInterval(() => { if (currentPerf < {{ $lead->performance_score }}) currentPerf++; else clearInterval(perf); }, 20);
                            let mob = setInterval(() => { if (currentMob < {{ $lead->mobile_score }}) currentMob++; else clearInterval(mob); }, 20);
                            let bp = setInterval(() => { if (currentBp < {{ $lead->best_practices_score ?? 0 }}) currentBp++; else clearInterval(bp); }, 20);
                        }, 300);
                     ">
                    @foreach([
                        ['SEO', 'currentSc'],
                        ['Velocidade', 'currentPerf'],
                        ['UX / Mobile', 'currentMob'],
                        ['Boas práticas', 'currentBp'],
                    ] as $g)
                        <div class="rounded-2xl border border-white/10 p-5 flex flex-col items-center">
                            <div class="relative w-24 h-24 flex items-center justify-center">
                                <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-white/[0.06]" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path :class="{{ $g[1] }} >= 80 ? 'text-emerald-400' : ({{ $g[1] }} >= 50 ? 'text-amber-400' : 'text-red-400')"
                                          :stroke-dasharray="{{ $g[1] }} + ', 100'" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" class="transition-all duration-300"/>
                                </svg>
                                <span class="font-display font-black text-2xl"
                                      :class="{{ $g[1] }} >= 80 ? 'text-emerald-400' : ({{ $g[1] }} >= 50 ? 'text-amber-400' : 'text-red-400')"
                                      x-text="{{ $g[1] }}"></span>
                            </div>
                            <span class="mt-4 text-[11px] font-extrabold uppercase tracking-widest text-white/60 text-center">{{ $g[0] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Auditoria social (Instagram) --}}
        @if(isset($lead->tipo_analise) && $lead->tipo_analise === 'redes_sociais' && isset($lead->ig_followers))
            <section class="mb-16">
                <div class="flex items-baseline justify-between mb-8 pb-4 border-b border-white/10">
                    <h2 class="font-display font-extrabold text-xl md:text-2xl text-white tracking-tight">Auditoria do Instagram</h2>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-white/40">Apify</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-white/10 p-6">
                        <p class="text-[11px] font-extrabold uppercase tracking-widest text-white/40 mb-3">Seguidores</p>
                        <p class="font-display font-black text-4xl text-white">{{ number_format($lead->ig_followers, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 p-6">
                        <p class="text-[11px] font-extrabold uppercase tracking-widest text-white/40 mb-3">Posts</p>
                        <p class="font-display font-black text-4xl text-white">{{ number_format($lead->ig_posts, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if(!empty($lead->ig_bio))
                    <div class="mt-4 rounded-2xl border border-white/10 p-6">
                        <p class="text-[11px] font-extrabold uppercase tracking-widest text-white/40 mb-3">Bio extraída</p>
                        <p class="text-white/85 italic">"{{ $lead->ig_bio }}"</p>
                    </div>
                @endif
            </section>
        @endif

        {{-- Parecer da IA --}}
        <section class="mb-16">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-white/10">
                <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-6 h-6">
                <h2 class="font-display font-extrabold text-xl md:text-2xl text-white tracking-tight">Parecer estratégico</h2>
                <span class="ml-auto text-[11px] font-extrabold uppercase tracking-widest text-white/40">BruceIA</span>
            </div>

            <div class="bruce-prose">
                {!! $resultado !!}
            </div>
        </section>

        {{-- CTA final --}}
        <section class="mt-20 pt-16 border-t border-white/10 text-center">
            <h2 class="font-display font-extrabold text-white leading-tight tracking-tight text-3xl md:text-4xl mb-4">
                Quer transformar isso em resultado?
            </h2>
            <p class="text-white/60 leading-relaxed max-w-xl mx-auto mb-10">
                Uma equipe da NC5 traduz o parecer em plano executável — posicionamento, funil e automação. Sem enrolação.
            </p>
            <a href="{{ route('contato.index') }}"
               class="group inline-flex items-center gap-3 bg-white text-black hover:bg-[#FF7A1A] hover:text-white font-bold py-4 px-8 rounded-xl text-sm transition-colors">
                Falar com um estrategista
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </section>
    </div>
</div>
@endsection
