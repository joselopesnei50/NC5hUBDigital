@extends('layouts.public')

@section('title', $post->titulo . ' · NC5 Hub')

@section('content')
    <article class="pt-16 lg:pt-20 pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate hover:text-bruce transition-colors mb-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar para os insights
            </a>

            <header class="mb-12">
                <p class="text-xs font-bold uppercase tracking-widest text-magenta mb-4">{{ $post->created_at->translatedFormat('d \d\e F \d\e Y') }}</p>
                <h1 class="font-display font-bold text-4xl md:text-6xl text-ink leading-[1.05] tracking-tight">{{ $post->titulo }}</h1>
            </header>

            <div class="aspect-[16/9] rounded-3xl bg-gradient-to-br from-mist via-white to-mist border border-black/5 flex items-center justify-center overflow-hidden mb-12">
                <span class="font-display font-black text-8xl text-slate/20">NC5</span>
            </div>

            <div class="prose prose-lg max-w-none prose-headings:font-display prose-headings:text-ink prose-a:text-magenta prose-strong:text-ink text-slate leading-relaxed">
                {!! nl2br(e($post->conteudo)) !!}
            </div>

            <hr class="my-16 border-black/5">

            <div class="bg-mist rounded-3xl p-8 flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-16 h-16 bg-ink rounded-full flex-shrink-0 flex items-center justify-center text-white font-display font-bold text-2xl">N</div>
                <div class="flex-grow">
                    <h4 class="font-bold text-ink text-lg">Equipe NC5</h4>
                    <p class="text-slate text-sm mt-1">Especialistas em performance, branding e execução premium.</p>
                </div>
                <a href="{{ route('analise.index') }}" class="bg-ink hover:bg-bruce text-white px-5 py-2.5 rounded-full text-sm font-bold transition-colors whitespace-nowrap">Falar com a equipe</a>
            </div>
        </div>
    </article>
@endsection
