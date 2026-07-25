@extends('layouts.public')

@section('title', 'Resultado da Análise - NC5 Hub')

@push('styles')
<style>
    /* Prose Styling for AI HTML Content */
    .bruce-prose {
        max-width: none;
        color: #334155;
        font-family: 'Inter', sans-serif;
        line-height: 1.75;
    }
    .bruce-prose h3 {
        font-family: 'Fraunces', serif;
        color: #0A1128;
        font-size: 1.5rem;
        margin-top: 2.5rem;
        margin-bottom: 1.5rem;
        padding-left: 1.25rem;
        border-left: 4px solid #FF7A1A;
        background-color: #f8fafc;
        padding: 1.25rem;
        border-radius: 0 0.5rem 0.5rem 0;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .bruce-prose h3:first-child {
        margin-top: 0;
    }
    .bruce-prose p {
        margin-bottom: 1.25rem;
    }
    .bruce-prose ul {
        list-style-type: none;
        padding-left: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .bruce-prose li {
        position: relative;
        margin-bottom: 0.75rem;
    }
    .bruce-prose li::before {
        content: "•";
        color: #FF7A1A;
        font-weight: bold;
        position: absolute;
        left: -1rem;
        font-size: 1.2rem;
        line-height: 1;
        top: 0.2rem;
    }
    .bruce-prose strong {
        color: #0A1128;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 font-sans text-slate-800 pb-20">
    <!-- Hero Section -->
    <section class="bg-[#0A1128] pt-24 pb-16 relative overflow-hidden">
        <div class="container mx-auto px-4 max-w-5xl relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Relatório Concluído
                </span>
            </div>
            
            <h1 class="text-3xl md:text-5xl font-display text-white mb-4 leading-tight">
                Análise de <span class="text-[#FF7A1A]">{{ $lead->tipo_analise ?? 'Projeto' }}</span>
            </h1>
            
            @if(isset($lead->url_analise) && $lead->url_analise)
            <div class="inline-flex items-center gap-2 text-slate-300 bg-white/5 px-4 py-2 rounded-lg border border-white/10 backdrop-blur-sm">
                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                <a href="{{ $lead->url_analise }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors duration-200">
                    {{ $lead->url_analise }}
                </a>
            </div>
            @endif
        </div>
    </section>

    <!-- Info Strip -->
    <div class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="flex flex-wrap items-center justify-between py-3 text-sm text-slate-600 gap-4">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="font-medium">{{ $lead->nome ?? 'Cliente' }}</span>
                    </div>
                    <div class="hidden sm:flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ isset($lead->created_at) ? $lead->created_at->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="container mx-auto px-4 max-w-5xl py-10">
        
        <!-- Report Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 overflow-hidden mb-12 border border-slate-100">
            <!-- Card Header -->
            <div class="bg-[#0A1128] px-6 py-5 flex items-center justify-between border-b-4 border-[#FF7A1A]">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="h-8 w-auto">
                    <h2 class="text-xl font-display font-semibold text-white">Parecer Estratégico do Bruce</h2>
                </div>
            </div>
            
            @if(isset($lead->tipo_analise) && $lead->tipo_analise === 'site' && isset($lead->seo_score))
            <!-- Technical Audit Gauges -->
            <div class="bg-slate-50 border-b border-slate-100 p-6 md:p-10 flex flex-col items-center justify-center">
                <h3 class="font-display font-bold text-xl text-slate-800 mb-8">Auditoria Técnica Relâmpago</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 w-full max-w-3xl" x-data="{ currentSc: 0, currentPerf: 0, currentMob: 0 }" x-init="
                    setTimeout(() => {
                        let sc = setInterval(() => { if (currentSc < {{ $lead->seo_score }}) currentSc++; else clearInterval(sc); }, 20);
                        let perf = setInterval(() => { if (currentPerf < {{ $lead->performance_score }}) currentPerf++; else clearInterval(perf); }, 20);
                        let mob = setInterval(() => { if (currentMob < {{ $lead->mobile_score }}) currentMob++; else clearInterval(mob); }, 20);
                    }, 500);
                ">
                    <!-- SEO Score -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-32 h-32 flex items-center justify-center rounded-full bg-slate-200">
                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-200" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path :class="currentSc >= 80 ? 'text-emerald-500' : (currentSc >= 50 ? 'text-amber-500' : 'text-red-500')" :stroke-dasharray="currentSc + ', 100'" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" class="transition-all duration-300" />
                            </svg>
                            <span class="font-display font-black text-3xl" :class="currentSc >= 80 ? 'text-emerald-500' : (currentSc >= 50 ? 'text-amber-500' : 'text-red-500')" x-text="currentSc"></span>
                        </div>
                        <span class="mt-4 font-bold text-slate-700 text-sm uppercase tracking-wider">SEO On-Page</span>
                    </div>

                    <!-- Performance Score -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-32 h-32 flex items-center justify-center rounded-full bg-slate-200">
                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-200" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path :class="currentPerf >= 80 ? 'text-emerald-500' : (currentPerf >= 50 ? 'text-amber-500' : 'text-red-500')" :stroke-dasharray="currentPerf + ', 100'" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" class="transition-all duration-300" />
                            </svg>
                            <span class="font-display font-black text-3xl" :class="currentPerf >= 80 ? 'text-emerald-500' : (currentPerf >= 50 ? 'text-amber-500' : 'text-red-500')" x-text="currentPerf"></span>
                        </div>
                        <span class="mt-4 font-bold text-slate-700 text-sm uppercase tracking-wider">Velocidade</span>
                    </div>

                    <!-- Mobile Score -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-32 h-32 flex items-center justify-center rounded-full bg-slate-200">
                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-200" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path :class="currentMob >= 80 ? 'text-emerald-500' : (currentMob >= 50 ? 'text-amber-500' : 'text-red-500')" :stroke-dasharray="currentMob + ', 100'" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" class="transition-all duration-300" />
                            </svg>
                            <span class="font-display font-black text-3xl" :class="currentMob >= 80 ? 'text-emerald-500' : (currentMob >= 50 ? 'text-amber-500' : 'text-red-500')" x-text="currentMob"></span>
                        </div>
                        <span class="mt-4 font-bold text-slate-700 text-sm uppercase tracking-wider">UX / Mobile</span>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- AI Content -->
            <div class="p-6 md:p-10">
                <div class="bruce-prose">
                    {!! $resultado !!}
                </div>
            </div>
            
            <!-- Card Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FF7A1A]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM11.146 6.146a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L13.293 10l-2.147-2.146a.5.5 0 010-.708z" clip-rule="evenodd"></path><path fill-rule="evenodd" d="M3.5 10a.5.5 0 01.5-.5h8a.5.5 0 010 1H4a.5.5 0 01-.5-.5z" clip-rule="evenodd"></path></svg>
                    <span>Gerado por BruceIA</span>
                </div>
                <span>{{ now()->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-[#0A1128] rounded-2xl p-8 md:p-12 text-center relative overflow-hidden shadow-2xl">
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mb-6 backdrop-blur-md border border-white/10">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="h-7 w-7">
                </div>
                
                <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">Gostou das oportunidades?</h2>
                
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    A NC5 Hub tem uma equipe de estrategistas digitais prontos para transformar estes insights em resultados tangíveis para o seu negócio.
                </p>
                
                <a href="{{ route('contato.index') }}" class="inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#e66a12] text-white font-semibold py-4 px-8 rounded-xl transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    Falar com um estrategista NC5
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
        
    </div>
</div>
@endsection
