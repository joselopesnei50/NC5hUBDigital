@extends('layouts.public')

@section('title', 'Insights · NC5 Hub')

@section('content')
    <!-- Header -->
    <section class="relative overflow-hidden">
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-magenta/10 rounded-full blur-[130px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 lg:pt-32">
            <p class="text-xs font-bold uppercase tracking-widest text-magenta mb-4">Insights & Estratégia</p>
            <h1 class="font-display font-bold text-5xl md:text-7xl text-ink leading-[0.95] tracking-tight max-w-3xl">
                Nosso <em class="not-italic gradient-text">radar</em> sobre marca, mídia e IA.
            </h1>
            <p class="mt-6 text-lg text-slate max-w-2xl">Textos curtos, opinativos e prontos para o próximo teste rápido.</p>
        </div>
    </section>

    <!-- Grid -->
    <section class="pb-24 lg:pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    <a href="{{ route('blog.post', $post->slug) }}" class="group flex flex-col">
                        <div class="aspect-[4/3] rounded-3xl bg-gradient-to-br from-mist via-white to-mist border border-black/5 flex items-center justify-center overflow-hidden mb-5 group-hover:shadow-2xl group-hover:shadow-ink/5 transition-all relative">
                            <span class="font-display font-black text-7xl text-slate/20 group-hover:scale-110 group-hover:text-bruce/50 transition-all">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <p class="text-xs font-bold text-magenta uppercase tracking-widest">{{ $post->created_at->format('d M Y') }}</p>
                        <h3 class="mt-2 font-display font-bold text-2xl text-ink group-hover:text-bruce transition-colors leading-tight line-clamp-2">{{ $post->titulo }}</h3>
                        <p class="mt-3 text-sm text-slate line-clamp-3">{{ Str::limit(strip_tags($post->conteudo), 150) }}</p>
                        <div class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-ink group-hover:gap-3 transition-all">
                            Ler artigo
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20">
                        <p class="text-lg text-slate">Nenhum artigo publicado no momento.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-16">
                {{ $posts->onEachSide(1)->links() }}
            </div>
        </div>
    </section>
@endsection
