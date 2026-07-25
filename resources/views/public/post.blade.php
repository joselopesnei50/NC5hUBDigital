@extends('layouts.public')

@section('title', $post->titulo . ' · NC5 Hub')

@section('content')
    <article class="pt-24 lg:pt-32 pb-24 relative overflow-hidden">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/60 hover:text-[#FF7A1A] transition-colors mb-10 group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar para os insights
            </a>

            <header class="mb-12">
                <p class="text-xs font-bold uppercase tracking-widest text-[#FF7A1A] mb-4">{{ $post->created_at->translatedFormat('d \d\e F \d\e Y') }}</p>
                <h1 class="font-display font-bold text-4xl md:text-5xl lg:text-6xl text-white leading-[1.1] tracking-tight">{{ $post->titulo }}</h1>
            </header>

            <div class="aspect-[16/9] rounded-3xl glass-card flex items-center justify-center overflow-hidden mb-12 border border-white/5 relative group">
                <!-- Overlay brilhante sutil -->
                <div class="absolute inset-0 bg-gradient-to-tr from-transparent to-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                <span class="font-display font-black text-8xl text-white/10 drop-shadow-xl">NC5</span>
            </div>

            <!-- Ajuste fino da tipografia para leitura no escuro -->
            <div class="prose prose-lg max-w-none prose-p:text-white/70 prose-headings:font-display prose-headings:text-white prose-a:text-[#FF7A1A] prose-strong:text-white prose-strong:font-bold prose-ul:text-white/70 prose-ol:text-white/70 leading-relaxed text-[1.1rem]">
                {!! nl2br(e($post->conteudo)) !!}
            </div>

            <hr class="my-16 border-white/10">

            <!-- Card de Autor Dark -->
            <div class="glass-card rounded-3xl p-8 flex flex-col sm:flex-row items-start sm:items-center gap-6 border border-white/5">
                <div class="w-16 h-16 bg-[#FF7A1A]/10 border border-[#FF7A1A]/20 rounded-full flex-shrink-0 flex items-center justify-center text-[#FF7A1A] font-display font-bold text-2xl shadow-[0_0_15px_rgba(255,122,26,0.2)]">N</div>
                <div class="flex-grow">
                    <h4 class="font-bold text-white text-lg">Equipe NC5</h4>
                    <p class="text-white/60 text-sm mt-1">Especialistas em performance, branding e execução premium.</p>
                </div>
                <a href="{{ route('analise.index') }}" class="bg-white hover:bg-[#FF7A1A] hover:text-white text-black px-6 py-3 rounded-full text-sm font-bold transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)] hover:shadow-[0_0_20px_rgba(255,122,26,0.4)] whitespace-nowrap">Falar com a equipe</a>
            </div>
        </div>
    </article>
@endsection
