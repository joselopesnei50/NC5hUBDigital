<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NC5 HUB') }} — Portal do Cliente</title>

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
                            magenta: '#E63888', 
                            mist: '#F8FAFC', 
                            slateText: '#64748B', 
                            bruce: '#FF7A1A', 
                            bruceDark: '#E5651A', 
                            bruceInk: '#090D1A'
                        },
                        fontFamily: { 
                            sans: ['Plus Jakarta Sans','sans-serif'], 
                            display: ['Outfit','sans-serif'] 
                        },
                        boxShadow: {
                            'premium': '0 10px 30px -10px rgba(10, 17, 40, 0.08), 0 4px 6px -2px rgba(10, 17, 40, 0.03)',
                            'glow': '0 0 20px rgba(230, 56, 136, 0.15)',
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
            .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border: 1px solid rgba(226, 232, 240, 0.9); }
        </style>
    </head>
    <body class="bg-mist text-ink h-full font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="flex h-screen overflow-hidden">

            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-ink/60 backdrop-blur-sm md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

            @include('layouts.navigation')

            <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden bg-mist">
                <!-- Mobile Bar -->
                <header class="bg-ink text-white border-b border-white/10 z-10 p-4 flex justify-between items-center md:hidden">
                    <a href="{{ route('customer.index') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo-claro.svg') }}" alt="NC5 Hub Digital" class="h-7 w-auto">
                    </a>
                    <button @click="sidebarOpen = true" class="text-white hover:text-bruce p-2 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </header>

                <main class="flex-1 p-4 sm:p-8 max-w-7xl mx-auto w-full">
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
                </main>
            </div>
        </div>
    </body>
</html>
