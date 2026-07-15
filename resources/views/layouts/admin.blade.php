<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NC5 HUB Admin') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800|fraunces:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { ink: '#0A1128', magenta: '#E63888', mist: '#F4F5F7', slate: '#8A8F9C', bruce: '#FF7A1A', bruceDark: '#E5651A', bruceInk: '#0A0A0B' },
                fontFamily: { sans: ['Inter','sans-serif'], display: ['Fraunces','Georgia','serif'] },
            }}
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        .font-display { font-family: 'Fraunces', Georgia, serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-mist text-ink" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-ink/50 md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

        <nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 md:relative md:translate-x-0 bg-white border-r border-black/5 w-64 h-full flex flex-col flex-shrink-0 shadow-xl md:shadow-none transition-transform duration-300">

            <div class="overflow-y-auto no-scrollbar flex-1">
                <div class="h-20 flex items-center px-6 border-b border-black/5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.svg') }}" alt="NC5 Logo" class="h-7 w-auto">
                    </a>
                </div>

                <div class="px-4 py-6 space-y-1">
                    @php
                        $navMain = [
                            ['route'=>'admin.dashboard', 'label'=>'Dashboard', 'match'=>'admin.dashboard', 'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                            ['route'=>'admin.clientes.index', 'label'=>'Clientes', 'match'=>'admin.clientes.*', 'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['route'=>'admin.contratos.index', 'label'=>'Contratos', 'match'=>'admin.contratos.*', 'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route'=>'admin.faturas.index', 'label'=>'Faturas', 'match'=>'admin.faturas.*', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['route'=>'admin.materiais.index', 'label'=>'Materiais', 'match'=>'admin.materiais.*', 'icon'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['route'=>'admin.tickets.index', 'label'=>'Suporte & Tickets', 'match'=>'admin.tickets.*', 'icon'=>'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                        ];
                        $navCms = [
                            ['route'=>'admin.paginas.index', 'label'=>'Páginas', 'match'=>'admin.paginas.*', 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ['route'=>'admin.posts.index', 'label'=>'Blog', 'match'=>'admin.posts.*', 'icon'=>'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8'],
                            ['route'=>'admin.servicos.index', 'label'=>'Catálogo de Serviços', 'match'=>'admin.servicos.*', 'icon'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                        ];
                    @endphp

                    @foreach($navMain as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs($item['match']) ? 'bg-ink text-white font-semibold shadow-md shadow-ink/10' : 'text-slate hover:text-ink hover:bg-mist' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <div class="pt-6 mt-2">
                        <p class="px-3 text-[10px] font-bold text-slate uppercase tracking-widest mb-3">CMS Site Público</p>
                        @foreach($navCms as $item)
                            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs($item['match']) ? 'bg-ink text-white font-semibold shadow-md shadow-ink/10' : 'text-slate hover:text-ink hover:bg-mist' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="pt-6 mt-2">
                        <p class="px-3 text-[10px] font-bold text-slate uppercase tracking-widest mb-3">Sistema</p>
                        <a href="{{ route('admin.configuracoes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('admin.configuracoes.*') ? 'bg-ink text-white font-semibold shadow-md shadow-ink/10' : 'text-slate hover:text-ink hover:bg-mist' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Configurações
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-black/5 flex-shrink-0">
                <div class="flex items-center gap-3 px-2 py-2 mb-1">
                    <div class="w-10 h-10 rounded-xl bg-ink flex items-center justify-center text-sm font-bold text-white">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold text-ink truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate font-bold uppercase tracking-wider">Super Admin</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-magenta hover:bg-bruce/5 rounded-xl transition-colors flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Encerrar sessão
                    </button>
                </form>
            </div>
        </nav>

        <main class="flex-1 flex flex-col overflow-hidden bg-mist">
            <header class="bg-white border-b border-black/5 z-10 p-4 flex justify-between items-center md:hidden">
                <img src="{{ asset('images/logo.svg') }}" alt="NC5 Logo" class="h-6 w-auto">
                <button @click="sidebarOpen = true" class="text-ink hover:text-bruce p-2 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </header>

            <div class="flex-1 overflow-y-auto p-4 sm:p-8">
                @if (isset($header))
                    <div class="mb-8">
                        {{ $header }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
