<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NC5 HUB') }} — Painel Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800|outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: { 
                extend: {
                    colors: { 
                        ink: '#0A1128', 
                        inkLight: '#141D38',
                        mist: '#F8FAFC', 
                        slateText: '#64748B', 
                        bruce: '#FF7A1A', 
                        bruceDark: '#E5651A', 
                        bruceInk: '#090D1A',
                        emeraldLight: '#ECFDF5',
                        amberLight: '#FFFBEB'
                    },
                    fontFamily: { 
                        sans: ['Plus Jakarta Sans','sans-serif'], 
                        display: ['Outfit','sans-serif'] 
                    },
                    boxShadow: {
                        'premium': '0 10px 30px -10px rgba(10, 17, 40, 0.08), 0 4px 6px -2px rgba(10, 17, 40, 0.03)',
                        'glow': '0 0 20px rgba(255, 122, 26, 0.15)',
                        'bruceGlow': '0 0 20px rgba(255, 122, 26, 0.2)'
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; }
        .font-display { font-family: 'Outfit', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .glass-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(226, 232, 240, 0.8); }
        .glass-dark { background: rgba(10, 17, 40, 0.95); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="bg-mist text-ink h-full font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        <!-- Mobile overlay -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-ink/60 backdrop-blur-sm md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

        <!-- Sidebar Admin -->
        <nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 md:relative md:translate-x-0 bg-ink text-white w-64 h-full flex flex-col flex-shrink-0 shadow-2xl md:shadow-none transition-transform duration-300">

            <div class="overflow-y-auto no-scrollbar flex-1 px-4 py-6">
                <!-- Logo & Brand -->
                <div class="h-16 flex items-center px-3 mb-6 border-b border-white/10">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-claro.svg') }}" alt="NC5 Hub Digital" class="h-8 w-auto">
                        <span class="text-[9px] font-extrabold text-bruce uppercase tracking-widest bg-bruce/10 px-2 py-0.5 rounded-md border border-bruce/20">Admin</span>
                    </a>
                </div>

                <div class="space-y-1">
                    @php
                        $navMain = [
                            ['route'=>'admin.dashboard', 'label'=>'Dashboard', 'match'=>'admin.dashboard', 'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                            ['route'=>'admin.clientes.index', 'label'=>'Clientes', 'match'=>'admin.clientes.*', 'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['route'=>'admin.contratos.index', 'label'=>'Contratos', 'match'=>'admin.contratos.*', 'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route'=>'admin.faturas.index', 'label'=>'Faturas', 'match'=>'admin.faturas.*', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['route'=>'admin.materiais.index', 'label'=>'Produção & Materiais', 'match'=>'admin.materiais.*', 'icon'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['route'=>'admin.tickets.index', 'label'=>'Suporte & Tickets', 'match'=>'admin.tickets.*', 'icon'=>'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                            ['route'=>'admin.contatos.index', 'label'=>'Contatos', 'match'=>'admin.contatos.*', 'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ];
                        $navCms = [
                            ['route'=>'admin.paginas.index', 'label'=>'Páginas', 'match'=>'admin.paginas.*', 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ['route'=>'admin.posts.index', 'label'=>'Blog', 'match'=>'admin.posts.*', 'icon'=>'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8'],
                            ['route'=>'admin.servicos.index', 'label'=>'Catálogo de Serviços', 'match'=>'admin.servicos.*', 'icon'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                        ];
                    @endphp

                    <p class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest pt-2 pb-2">Gestão Principal</p>

                    @foreach($navMain as $item)
                        @php $isActive = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-[#FF7A1A] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <div class="pt-6">
                        <p class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest pb-2">CMS Site Público</p>
                        @foreach($navCms as $item)
                            @php $isActive = request()->routeIs($item['match']); @endphp
                            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-[#FF7A1A] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="pt-6">
                        <p class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest pb-2">Configurações</p>
                        <a href="{{ route('admin.configuracoes.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.configuracoes.*') ? 'bg-[#FF7A1A] text-white shadow-md font-bold' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Configurações Globais
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile & Logout Footer -->
            <div class="p-4 border-t border-white/10 flex-shrink-0 bg-inkLight/50">
                <div class="flex items-center gap-3 px-2 py-2 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-bruce flex items-center justify-center text-sm font-bold text-white shadow-md">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-bruce font-bold uppercase tracking-wider">Super Administrator</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-xs text-rose-400 hover:text-white hover:bg-rose-500/20 rounded-xl transition-all flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Encerrar Sessão
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col overflow-hidden bg-mist">
            <!-- Top Mobile Bar -->
            <header class="bg-ink text-white border-b border-white/10 z-10 p-4 flex justify-between items-center md:hidden">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-claro.svg') }}" alt="NC5 Hub Digital" class="h-7 w-auto">
                </a>
                <button @click="sidebarOpen = true" class="text-white hover:text-bruce p-2 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </header>

            <div class="flex-1 overflow-y-auto p-4 sm:p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-base">✓</span>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-xl font-bold">&times;</button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-base">✕</span>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-xl font-bold">&times;</button>
                    </div>
                @endif

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
