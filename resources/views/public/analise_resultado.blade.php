@extends('layouts.public')

@section('title', 'Resultado da Análise - NC5 Hub')

@push('styles')
<style>
    /* Prose Styling for AI HTML Content - Dark Mode */
    .bruce-prose {
        max-width: none;
        color: rgba(255, 255, 255, 0.7);
        font-family: 'Inter', sans-serif;
        line-height: 1.75;
    }
    .bruce-prose h3 {
        font-family: 'Fraunces', serif;
        color: #FFFFFF;
        font-size: 1.5rem;
        margin-top: 2.5rem;
        margin-bottom: 1.5rem;
        padding-left: 1.25rem;
        border-left: 4px solid #FF7A1A;
        background-color: rgba(255, 122, 26, 0.05);
        padding: 1.25rem;
        border-radius: 0 0.5rem 0.5rem 0;
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
        color: #FFFFFF;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#050505] font-sans text-white pb-20 relative">
    <!-- Glow de Fundo -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-[#FF7A1A]/10 blur-[150px] rounded-full pointer-events-none"></div>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 relative overflow-hidden z-10">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(52,211,153,0.2)]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Relatório Concluído
                </span>
            </div>
            
            <h1 class="text-3xl md:text-5xl font-display text-white mb-4 leading-tight tracking-tight">
                Análise de <span class="text-[#FF7A1A]">{{ $lead->tipo_analise ?? 'Projeto' }}</span>
            </h1>
            
            @if(isset($lead->url_analise) && $lead->url_analise)
            <div class="inline-flex items-center gap-2 text-white/60 bg-white/5 px-4 py-2 rounded-lg border border-white/10 backdrop-blur-sm">
                <svg class="w-5 h-5 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                <a href="{{ $lead->url_analise }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors duration-200">
                    {{ $lead->url_analise }}
                </a>
            </div>
            @endif
        </div>
    </section>

    <!-- Info Strip -->
    <div class="border-y border-white/5 bg-white/5 backdrop-blur-md sticky top-0 z-30">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="flex flex-wrap items-center justify-between py-3 text-sm text-white/60 gap-4">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="font-medium text-white/90">{{ $lead->nome ?? 'Cliente' }}</span>
                    </div>
                    <div class="hidden sm:flex items-center gap-2">
                        <svg class="w-4 h-4 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ isset($lead->created_at) ? $lead->created_at->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="container mx-auto px-4 max-w-5xl py-10 relative z-10">
        
        <!-- Report Card -->
        <div class="glass-card rounded-2xl overflow-hidden mb-12 border border-white/10 shadow-[0_0_30px_rgba(0,0,0,0.5)] relative">
            
            <!-- Card Header -->
            <div class="bg-black/40 px-6 py-5 flex items-center justify-between border-b-2 border-[#FF7A1A]/50">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="h-8 w-auto filter drop-shadow-[0_0_8px_rgba(255,122,26,0.5)]">
                    <h2 class="text-xl font-display font-semibold text-white tracking-wide">Parecer Estratégico do Bruce</h2>
                </div>
            </div>
            
            @if(isset($lead->tipo_analise) && $lead->tipo_analise === 'site' && isset($lead->seo_score))
            <!-- Technical Audit Gauges -->
            <div class="bg-white/5 border-b border-white/5 p-6 md:p-10 flex flex-col items-center justify-center relative">
                <!-- LCP Alert Badge -->
                @if(isset($lead->lcp_time) && $lead->lcp_time > 0)
                <div class="absolute top-0 -translate-y-1/2 flex items-center justify-center w-full">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs md:text-sm font-bold shadow-[0_0_15px_rgba(0,0,0,0.5)] border 
                        {{ $lead->lcp_time > 2.5 ? 'bg-red-500/20 text-red-400 border-red-500/30' : 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Tempo de Carregamento (LCP): {{ $lead->lcp_time }} segundos
                    </span>
                </div>
                @endif
                
                <h3 class="font-display font-bold text-xl text-white mb-8 mt-4 text-center">Auditoria Técnica Oficial (Lighthouse)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 w-full max-w-4xl" x-data="{ currentSc: 0, currentPerf: 0, currentMob: 0, currentBp: 0 }" x-init="
                    setTimeout(() => {
                        let sc = setInterval(() => { if (currentSc < {{ $lead->seo_score }}) currentSc++; else clearInterval(sc); }, 20);
                        let perf = setInterval(() => { if (currentPerf < {{ $lead->performance_score }}) currentPerf++; else clearInterval(perf); }, 20);
                        let mob = setInterval(() => { if (currentMob < {{ $lead->mobile_score }}) currentMob++; else clearInterval(mob); }, 20);
                        let bp = setInterval(() => { if (currentBp < {{ $lead->best_practices_score ?? 0 }}) currentBp++; else clearInterval(bp); }, 20);
                    }, 500);
                ">
                    <!-- SEO Score -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-24 h-24 md:w-32 md:h-32 flex items-center justify-center rounded-full bg-black/40 shadow-inner">
                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <path class="text-white/5" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path :class="currentSc >= 80 ? 'text-emerald-400' : (currentSc >= 50 ? 'text-amber-400' : 'text-red-400')" :stroke-dasharray="currentSc + ', 100'" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" class="transition-all duration-300 drop-shadow-[0_0_5px_currentColor]" />
                            </svg>
                            <span class="font-display font-black text-2xl md:text-3xl" :class="currentSc >= 80 ? 'text-emerald-400' : (currentSc >= 50 ? 'text-amber-400' : 'text-red-400')" x-text="currentSc"></span>
                        </div>
                        <span class="mt-3 md:mt-4 font-bold text-white/70 text-xs md:text-sm uppercase tracking-wider text-center">SEO</span>
                    </div>

                    <!-- Performance Score -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-24 h-24 md:w-32 md:h-32 flex items-center justify-center rounded-full bg-black/40 shadow-inner">
                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <path class="text-white/5" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path :class="currentPerf >= 80 ? 'text-emerald-400' : (currentPerf >= 50 ? 'text-amber-400' : 'text-red-400')" :stroke-dasharray="currentPerf + ', 100'" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" class="transition-all duration-300 drop-shadow-[0_0_5px_currentColor]" />
                            </svg>
                            <span class="font-display font-black text-2xl md:text-3xl" :class="currentPerf >= 80 ? 'text-emerald-400' : (currentPerf >= 50 ? 'text-amber-400' : 'text-red-400')" x-text="currentPerf"></span>
                        </div>
                        <span class="mt-3 md:mt-4 font-bold text-white/70 text-xs md:text-sm uppercase tracking-wider text-center">Velocidade</span>
                    </div>

                    <!-- Mobile Score -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-24 h-24 md:w-32 md:h-32 flex items-center justify-center rounded-full bg-black/40 shadow-inner">
                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <path class="text-white/5" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path :class="currentMob >= 80 ? 'text-emerald-400' : (currentMob >= 50 ? 'text-amber-400' : 'text-red-400')" :stroke-dasharray="currentMob + ', 100'" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" class="transition-all duration-300 drop-shadow-[0_0_5px_currentColor]" />
                            </svg>
                            <span class="font-display font-black text-2xl md:text-3xl" :class="currentMob >= 80 ? 'text-emerald-400' : (currentMob >= 50 ? 'text-amber-400' : 'text-red-400')" x-text="currentMob"></span>
                        </div>
                        <span class="mt-3 md:mt-4 font-bold text-white/70 text-xs md:text-sm uppercase tracking-wider text-center">UX / Mobile</span>
                    </div>

                    <!-- Best Practices Score -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-24 h-24 md:w-32 md:h-32 flex items-center justify-center rounded-full bg-black/40 shadow-inner">
                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <path class="text-white/5" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path :class="currentBp >= 80 ? 'text-emerald-400' : (currentBp >= 50 ? 'text-amber-400' : 'text-red-400')" :stroke-dasharray="currentBp + ', 100'" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" class="transition-all duration-300 drop-shadow-[0_0_5px_currentColor]" />
                            </svg>
                            <span class="font-display font-black text-2xl md:text-3xl" :class="currentBp >= 80 ? 'text-emerald-400' : (currentBp >= 50 ? 'text-amber-400' : 'text-red-400')" x-text="currentBp"></span>
                        </div>
                        <span class="mt-3 md:mt-4 font-bold text-white/70 text-xs md:text-sm uppercase tracking-wider text-center">Boas Práticas</span>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($lead->tipo_analise) && $lead->tipo_analise === 'redes_sociais' && isset($lead->ig_followers))
            <!-- Social Media Audit Gauges -->
            <div class="bg-white/5 border-b border-white/5 p-6 md:p-10 flex flex-col items-center justify-center relative">
                <h3 class="font-display font-bold text-xl text-white mb-8 mt-4 text-center">Auditoria de Instagram (Apify Data)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl">
                    <!-- Seguidores -->
                    <div class="flex items-center gap-6 bg-black/40 p-6 rounded-2xl border border-white/10 shadow-inner">
                        <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gradient-to-tr from-[#E1306C] to-[#F56040] shadow-[0_0_15px_rgba(225,48,108,0.5)] text-white">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-white/60 text-sm font-bold uppercase tracking-wider mb-1">Seguidores Atuais</p>
                            <p class="font-display font-black text-3xl text-white">{{ number_format($lead->ig_followers, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Posts -->
                    <div class="flex items-center gap-6 bg-black/40 p-6 rounded-2xl border border-white/10 shadow-inner">
                        <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gradient-to-tr from-[#833AB4] to-[#C13584] shadow-[0_0_15px_rgba(131,58,180,0.5)] text-white">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-white/60 text-sm font-bold uppercase tracking-wider mb-1">Total de Posts</p>
                            <p class="font-display font-black text-3xl text-white">{{ number_format($lead->ig_posts, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                @if(!empty($lead->ig_bio))
                <div class="w-full max-w-2xl mt-6 bg-black/40 p-6 rounded-2xl border border-white/10 shadow-inner text-center">
                    <p class="text-white/60 text-sm font-bold uppercase tracking-wider mb-3">Bio Extraída</p>
                    <p class="text-white italic">"{{ $lead->ig_bio }}"</p>
                </div>
                @endif
            </div>
            @endif
            
            <!-- AI Content -->
            <div class="p-6 md:p-10 bg-transparent relative">
                <!-- Sutil gradiente de fundo no texto -->
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/20 pointer-events-none"></div>
                <div class="bruce-prose relative z-10">
                    {!! $resultado !!}
                </div>
            </div>
            
            <!-- Card Footer -->
            <div class="bg-black/60 px-6 py-4 border-t border-white/5 flex items-center justify-between text-sm text-white/40">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FF7A1A]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM11.146 6.146a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L13.293 10l-2.147-2.146a.5.5 0 010-.708z" clip-rule="evenodd"></path><path fill-rule="evenodd" d="M3.5 10a.5.5 0 01.5-.5h8a.5.5 0 010 1H4a.5.5 0 01-.5-.5z" clip-rule="evenodd"></path></svg>
                    <span>Gerado por BruceIA</span>
                </div>
                <span>{{ now()->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="glass-card rounded-2xl p-8 md:p-12 text-center relative overflow-hidden border border-[#FF7A1A]/20">
            <!-- Neon effect no CTA -->
            <div class="absolute inset-0 bg-gradient-to-tr from-[#FF7A1A]/10 to-transparent pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-[#FF7A1A]/10 flex items-center justify-center mb-6 border border-[#FF7A1A]/30 shadow-[0_0_20px_rgba(255,122,26,0.3)]">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="h-8 w-8">
                </div>
                
                <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">Gostou das oportunidades?</h2>
                
                <p class="text-white/70 max-w-2xl mx-auto mb-10 text-lg">
                    A NC5 Hub tem uma equipe de estrategistas digitais prontos para transformar estes insights em resultados tangíveis para o seu negócio.
                </p>
                
                <a href="{{ route('contato.index') }}" class="inline-flex items-center justify-center gap-3 bg-white text-black hover:bg-[#FF7A1A] hover:text-white font-bold py-4 px-8 rounded-full transition-all duration-300 shadow-[0_0_15px_rgba(255,255,255,0.1)] hover:shadow-[0_0_25px_rgba(255,122,26,0.5)]">
                    Falar com um estrategista NC5
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
        
    </div>
</div>
@endsection
