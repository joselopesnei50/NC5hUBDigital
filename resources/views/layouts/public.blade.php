<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>NC5</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/simbolo.svg') }}">
        <meta name="description" content="@yield('meta_description', 'Uma agência que combina design premium, tráfego pago e IA para escalar o seu negócio.')">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900|fraunces:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            ink: '#0A1128',
                            mist: '#F4F5F7',
                            slate: '#8A8F9C',
                            bruce: '#FF7A1A',
                            bruceDark: '#E5651A',
                            bruceInk: '#0A0A0B',
                            agDark: '#050505',
                            agCard: 'rgba(20, 20, 25, 0.65)',
                            agBorder: 'rgba(255, 255, 255, 0.08)',
                        },
                        animation: {
                            'float': 'float 8s ease-in-out infinite',
                            'glow': 'glow 4s ease-in-out infinite alternate',
                        },
                        keyframes: {
                            float: {
                                '0%, 100%': { transform: 'translateY(0)' },
                                '50%': { transform: 'translateY(-15px)' },
                            },
                            glow: {
                                '0%': { filter: 'drop-shadow(0 0 40px rgba(255,122,26,0.3))' },
                                '100%': { filter: 'drop-shadow(0 0 80px rgba(255,122,26,0.8))' },
                            }
                        },
                        fontFamily: {
                            sans: ['Inter', 'system-ui', 'sans-serif'],
                            display: ['Fraunces', 'Georgia', 'serif'],
                        },
                    }
                }
            }
        </script>

        <style>
            body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
            .font-display { font-family: 'Fraunces', Georgia, serif; font-optical-sizing: auto; }
            .glass {
                backdrop-filter: saturate(180%) blur(14px);
                -webkit-backdrop-filter: saturate(180%) blur(14px);
                background-color: rgba(10, 10, 12, 0.75);
                border-bottom: 1px solid rgba(255,255,255,0.05);
            }
            .glass-card {
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                background-color: rgba(20, 20, 25, 0.6);
                border: 1px solid rgba(255,255,255,0.1);
            }
            .reveal {
                opacity: 0;
                transform: translateY(30px) scale(0.98);
                transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1);
            }
            .reveal.active {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            .reveal-delay-1 { transition-delay: 100ms; }
            .reveal-delay-2 { transition-delay: 200ms; }
            .reveal-delay-3 { transition-delay: 300ms; }
            .reveal-delay-4 { transition-delay: 400ms; }

            
            @keyframes bruceFloat {
                0%, 100% {
                    transform: translateY(0px) rotate(0deg) scale(1);
                }
                50% {
                    transform: translateY(-18px) rotate(1.5deg) scale(1.03);
                }
            }

            @keyframes bruceAura {
                0%, 100% {
                    transform: scale(0.95);
                    opacity: 0.4;
                    filter: blur(80px);
                }
                50% {
                    transform: scale(1.15);
                    opacity: 0.85;
                    filter: blur(120px);
                }
            }

            .animate-bruce-logo {
                animation: bruceFloat 6s ease-in-out infinite;
            }

            .animate-bruce-aura {
                animation: bruceAura 5s ease-in-out infinite;
            }
        </style>
        @stack('styles')
    </head>
    <body class="bg-[#050505] text-white flex flex-col min-h-screen selection:bg-[#FF7A1A] selection:text-white" x-data="{ mobileNav: false }">

        <!-- Navbar -->
        <nav class="glass sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-claro.svg') }}" alt="NC5 Logo" class="h-9 w-auto">
                </a>

                <div class="hidden md:flex items-center gap-1 text-sm font-medium">
                    @php $items = [
                        ['route' => 'home', 'label' => 'Início'],
                        ['route' => 'servicos', 'label' => 'O que fazemos'],
                        ['route' => 'blog', 'label' => 'Insights'],
                        ['route' => 'analise.index', 'label' => 'Análise com IA'],
                        ['route' => 'contato.index', 'label' => 'Contato'],
                    ]; @endphp
                    @foreach($items as $item)
                        <a href="{{ route($item['route']) }}" class="px-4 py-2 rounded-full transition-colors {{ request()->routeIs($item['route']) ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex bg-white/10 hover:bg-[#FF7A1A] text-white px-5 py-2 rounded-full text-sm font-bold transition-all shadow-md">Meu Painel</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline text-sm font-semibold text-white/80 hover:text-[#FF7A1A] transition-colors px-3 py-2">Entrar</a>
                        <a href="{{ route('analise.index') }}" class="hidden md:inline-flex items-center gap-2 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg shadow-[#FF7A1A]/20">
                            Análise Gratuita
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @endauth
                    <button @click="mobileNav = !mobileNav" class="md:hidden p-2 rounded-lg hover:bg-black/5" aria-label="Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            <div x-show="mobileNav" x-transition class="md:hidden border-t border-white/5 bg-[#0A0A0A]" style="display: none;">
                <div class="px-4 py-3 flex flex-col gap-1">
                    @foreach($items as $item)
                        <a href="{{ route($item['route']) }}" class="px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs($item['route']) ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">{{ $item['label'] }}</a>
                    @endforeach
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white">Entrar</a>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-ink text-white relative overflow-hidden mt-auto">
            <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-bruce/10 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                    <div class="md:col-span-5">
                        <img src="{{ asset('images/logo-claro.svg') }}" alt="NC5" class="h-8 mb-6">
                        <p class="text-slate text-sm max-w-sm leading-relaxed">Marca, tecnologia e mídia paga na mesma mesa. Aceleramos negócios que estão prontos para o próximo salto.</p>
                        @auth
                            <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-white hover:text-bruce transition-colors">
                                Acessar meu painel
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        @else
                            <a href="{{ route('analise.index') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-white hover:text-bruce transition-colors">
                                Fazer análise gratuita com IA
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        @endauth
                    </div>

                    <div class="md:col-span-3">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Explorar</h4>
                        <ul class="space-y-3 text-sm text-white/70">
                            <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Início</a></li>
                            <li><a href="{{ route('servicos') }}" class="hover:text-white transition-colors">Serviços</a></li>
                            <li><a href="{{ route('blog') }}" class="hover:text-white transition-colors">Insights</a></li>
                            <li><a href="{{ route('analise.index') }}" class="hover:text-white transition-colors">Diagnóstico com IA</a></li>
                        </ul>
                    </div>

                    <div class="md:col-span-2">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Acesso</h4>
                        <ul class="space-y-3 text-sm text-white/70">
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Painel</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Entrar</a></li>
                            @endauth
                        </ul>
                    </div>

                    <div class="md:col-span-2">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Legal</h4>
                        <ul class="space-y-3 text-sm text-white/70">
                            <li><a href="#" class="hover:text-white transition-colors">Privacidade</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Termos</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-white/50">&copy; {{ date('Y') }} NC5 Hub. Todos os direitos reservados.</p>
                    <p class="text-xs text-white/50">Feito com estratégia. Do briefing ao pixel.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
