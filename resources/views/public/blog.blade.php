@extends('layouts.public')

@section('title', 'Insights & Estratégia · NC5 Hub')
@section('meta_description', 'Artigos práticos, visão crítica e novidades sobre inteligência artificial, tráfego pago de alta performance e design de autoridade.')

@section('content')
<main class="min-h-screen bg-[#050505] text-white relative overflow-hidden" x-data="{ searchQuery: '' }">

    {{-- Efeitos de Iluminação / Background Antigravity --}}
    <div class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-[#FF7A1A]/10 blur-[150px] rounded-full pointer-events-none"></div>
    <div class="absolute top-[35%] right-[-10%] w-[500px] h-[500px] bg-blue-600/10 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[20%] w-[500px] h-[500px] bg-[#FF7A1A]/5 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDAuNWg0ME0wLjUgMHY0MCIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiLz48L3N2Zz4=')] z-0 opacity-40 pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10 pt-20 sm:pt-28 pb-24 lg:pb-32">

        {{-- Header da Seção --}}
        <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
            <h1 class="font-display font-black text-4xl sm:text-5xl lg:text-7xl text-white tracking-tight leading-[1.05]">
                Nosso radar sobre marca, mídia & IA
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-white/70 font-normal leading-relaxed max-w-2xl mx-auto">
                Visão cirúrgica, testes rápidos e estratégias reais para transformar dados e inteligência artificial em lucro.
            </p>
        </div>

        @if($posts->count() > 0)
            @php
                $featuredPost = $posts->currentPage() == 1 ? $posts->first() : null;
                $gridPosts = $posts->currentPage() == 1 ? $posts->slice(1) : $posts;
            @endphp

            {{-- Post em Destaque (Apenas na página 1) --}}
            @if($featuredPost)
                <div class="mb-16 lg:mb-24">
                    <a href="{{ route('blog.post', $featuredPost->slug) }}" class="group block relative rounded-3xl overflow-hidden bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-[#FF7A1A]/50 transition-all duration-500 shadow-2xl hover:shadow-[0_0_50px_rgba(255,122,26,0.15)]">
                        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                            {{-- Visual / Badge --}}
                            <div class="lg:col-span-7 aspect-[16/10] lg:aspect-auto min-h-[300px] lg:min-h-[420px] bg-gradient-to-br from-white/[0.05] via-white/[0.02] to-transparent flex items-center justify-center relative overflow-hidden border-b lg:border-b-0 lg:border-r border-white/10">
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_rgba(255,122,26,0.18)_0%,_transparent_70%)] opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700"></div>
                                <span class="font-display font-black text-8xl sm:text-9xl text-white/10 group-hover:scale-105 group-hover:text-[#FF7A1A]/30 transition-all duration-700 select-none">
                                    01
                                </span>
                                <div class="absolute top-6 left-6 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/60 border border-white/15 backdrop-blur-md text-[#FF7A1A] text-xs font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF7A1A]"></span>
                                    Destaque Editorial
                                </div>
                                <div class="absolute bottom-6 left-6 text-white/40 text-xs font-mono">
                                    NC5 INTEL // ED. {{ $featuredPost->created_at->format('Y.m') }}
                                </div>
                            </div>

                            {{-- Conteúdo do Destaque --}}
                            <div class="lg:col-span-5 p-8 sm:p-10 lg:p-12 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-widest text-white/50 mb-4">
                                        <span class="text-[#FF7A1A]">{{ $featuredPost->created_at->translatedFormat('d \d\e M, Y') }}</span>
                                        <span>•</span>
                                        <span>{{ max(1, (int) ceil(str_word_count(strip_tags($featuredPost->conteudo)) / 200)) }} min de leitura</span>
                                    </div>

                                    <h2 class="font-display font-bold text-2xl sm:text-3xl lg:text-4xl text-white group-hover:text-[#FF7A1A] transition-colors duration-300 leading-snug">
                                        {{ $featuredPost->titulo }}
                                    </h2>

                                    <p class="mt-4 text-white/70 text-base leading-relaxed line-clamp-4 font-normal">
                                        {{ Str::limit(strip_tags($featuredPost->conteudo), 220) }}
                                    </p>
                                </div>

                                <div class="pt-8 mt-6 border-t border-white/5 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-2 text-sm font-bold text-white group-hover:text-[#FF7A1A] group-hover:gap-3 transition-all">
                                        Ler artigo completo
                                        <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </span>
                                    <span class="text-xs text-white/40 group-hover:text-white/70 transition-colors">Acessar →</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- Grid dos Demais Artigos --}}
            <div class="mb-16">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-white/10">
                    <h3 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-3">
                        <span class="w-2.5 h-2.5 bg-[#FF7A1A] rounded-full"></span>
                        Todos os Artigos
                    </h3>
                    <span class="text-xs text-white/50 font-mono">{{ $posts->total() }} publicações registradas</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($gridPosts as $post)
                        @php
                            $readingTime = max(1, (int) ceil(str_word_count(strip_tags($post->conteudo)) / 200));
                        @endphp
                        <a href="{{ route('blog.post', $post->slug) }}" class="group flex flex-col bg-[#0F1729]/60 hover:bg-[#0F1729]/90 backdrop-blur-md rounded-3xl p-6 border border-white/10 hover:border-[#FF7A1A]/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)]">
                            
                            {{-- Visual Thumbnail --}}
                            <div class="aspect-[16/10] rounded-2xl bg-gradient-to-br from-white/[0.04] to-transparent border border-white/5 flex items-center justify-center overflow-hidden mb-6 relative group-hover:border-white/15 transition-all">
                                <div class="absolute inset-0 bg-[#FF7A1A]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <span class="font-display font-black text-6xl text-white/10 group-hover:scale-110 group-hover:text-[#FF7A1A]/40 transition-transform duration-700 select-none">
                                    {{ str_pad($loop->iteration + ($posts->currentPage() == 1 ? 1 : ($posts->currentPage() - 1) * $posts->perPage()), 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-black/50 border border-white/10 text-[11px] font-mono text-white/60">
                                    {{ $readingTime }} min
                                </div>
                            </div>

                            {{-- Meta --}}
                            <div class="flex items-center gap-3 text-xs font-bold uppercase tracking-wider text-[#FF7A1A] mb-3">
                                <span>{{ $post->created_at->translatedFormat('d \d\e M, Y') }}</span>
                            </div>

                            {{-- Título --}}
                            <h4 class="font-display font-bold text-xl text-white group-hover:text-[#FF7A1A] transition-colors leading-snug line-clamp-2 mb-3">
                                {{ $post->titulo }}
                            </h4>

                            {{-- Excerpt --}}
                            <p class="text-sm text-white/65 line-clamp-3 leading-relaxed mb-6 font-normal flex-grow">
                                {{ Str::limit(strip_tags($post->conteudo), 140) }}
                            </p>

                            {{-- Footer Card --}}
                            <div class="pt-4 border-t border-white/5 flex items-center justify-between text-xs font-bold text-white/80 group-hover:text-[#FF7A1A] transition-colors">
                                <span>Ler artigo</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Paginação Customizada Dark --}}
            @if($posts->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="bg-[#0F1729]/80 backdrop-blur-md border border-white/10 rounded-2xl p-2 shadow-xl inline-block">
                        {{ $posts->onEachSide(1)->links() }}
                    </div>
                </div>
            @endif

        @else
            {{-- Estado Vazio Elegante --}}
            <div class="max-w-xl mx-auto text-center py-24 bg-[#0F1729]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-8 sm:p-12 shadow-2xl">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-[#FF7A1A]/10 border border-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-white tracking-tight">Novos artigos em produção</h3>
                <p class="text-white/60 text-sm mt-3 leading-relaxed">Nossa equipe está redigindo análises profundas sobre inteligência artificial e estratégias de crescimento que serão publicadas em breve.</p>
                <div class="mt-8">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm transition-all border border-white/10">
                        ← Voltar para a Home
                    </a>
                </div>
            </div>
        @endif

        {{-- Banner CTA Bruce IA --}}
        <div class="mt-20 lg:mt-32">
            <div class="relative overflow-hidden bg-gradient-to-r from-[#0F1729] via-[#141E33] to-[#0F1729] rounded-3xl p-8 sm:p-12 lg:p-16 border border-[#FF7A1A]/25 shadow-2xl">
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-[#FF7A1A]/15 blur-[120px] rounded-full pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div class="w-20 h-20 rounded-2xl bg-white/[0.04] border border-white/10 flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(255,122,26,0.25)]">
                            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="Bruce IA" class="w-12 h-12">
                        </div>
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] block mb-1">Diagnóstico Empresarial Instantâneo</span>
                            <h3 class="font-display font-bold text-2xl sm:text-3xl text-white">Quer descobrir onde sua marca está perdendo vendas?</h3>
                            <p class="text-white/70 text-sm sm:text-base mt-2 max-w-xl">
                                Nosso agente Bruce analisa sua presença digital, autoridade e oportunidades em menos de 60 segundos.
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('analise.index') }}" class="inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#FF8F3D] text-white px-8 py-4 rounded-full font-bold text-sm transition-all shadow-[0_0_25px_rgba(255,122,26,0.4)] hover:shadow-[0_0_35px_rgba(255,122,26,0.6)] shrink-0 hover:scale-105 active:scale-95">
                        Fazer Análise Gratuita
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection
