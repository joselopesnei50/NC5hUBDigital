<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $landingPage->titulo_pagina }}</title>
    <meta name="description" content="{{ $landingPage->meta_descricao ?: $landingPage->hero_subtitulo }}">
    <meta property="og:title" content="{{ $landingPage->titulo_pagina }}">
    <meta property="og:description" content="{{ $landingPage->meta_descricao ?: $landingPage->hero_subtitulo }}">
    <meta property="og:type" content="website">
    @if($landingPage->hero_image_path)
        <meta property="og:image" content="{{ asset('storage/' . $landingPage->hero_image_path) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --lp-primary: {{ $landingPage->cor_primaria ?: '#6366f1' }};
            --lp-secondary: {{ $landingPage->cor_secundaria ?: '#0f172a' }};
        }
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-lp-primary { background-color: var(--lp-primary); }
        .text-lp-primary { color: var(--lp-primary); }
        .border-lp-primary { border-color: var(--lp-primary); }
        .bg-lp-secondary { background-color: var(--lp-secondary); }
        .text-lp-secondary { color: var(--lp-secondary); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased text-slate-800 bg-white selection:bg-indigo-600 selection:text-white">

    @if(!$landingPage->is_published)
        <div class="bg-amber-500 text-slate-950 text-xs font-bold py-2.5 px-4 text-center sticky top-0 z-50 shadow-sm flex items-center justify-center gap-3 flex-wrap">
            <span>MODO PRÉ-VISUALIZAÇÃO (RASCUNHO) — Esta página ainda não está visível ao público.</span>
            @auth
                <a href="{{ route('customer.landing-page.edit') }}" class="underline font-extrabold hover:opacity-80">Voltar ao Editor</a>
            @endauth
        </div>
    @endif

    @yield('content')

    {{-- Botão Flutuante de WhatsApp --}}
    @if($landingPage->whatsappLimpo())
        <a href="{{ route('public.landing-page.whatsapp', ['slug' => $landingPage->slug]) }}"
           target="_blank" rel="noopener"
           aria-label="Conversar no WhatsApp"
           class="fixed bottom-6 right-6 z-40 flex items-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-5 py-3.5 rounded-full shadow-xl hover:scale-105 transition-all">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824z"/>
            </svg>
            <span class="hidden sm:inline text-sm">WhatsApp</span>
        </a>
    @endif

</body>
</html>
