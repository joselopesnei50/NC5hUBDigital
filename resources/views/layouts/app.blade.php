<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NC5 HUB') }}</title>

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

            @include('layouts.navigation')

            <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden bg-mist">
                <header class="bg-white border-b border-black/5 z-10 p-4 flex justify-between items-center md:hidden">
                    <img src="{{ asset('images/logo.svg') }}" alt="NC5 Logo" class="h-6 w-auto">
                    <button @click="sidebarOpen = true" class="text-ink hover:text-bruce p-2 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </header>

                <main class="flex-1 p-4 sm:p-8 max-w-7xl mx-auto w-full">
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
