<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>NC5</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/simbolo.svg') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800|fraunces:400,500,600,700,800&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <script>
            tailwind.config = {
                theme: { extend: {
                    colors: { ink: '#0A1128', mist: '#F4F5F7', slate: '#8A8F9C', bruce: '#FF7A1A', bruceDark: '#E5651A', bruceInk: '#0A0A0B' },
                    fontFamily: { sans: ['Inter','sans-serif'], display: ['Fraunces','Georgia','serif'] },
                }}
            }
        </script>

        <style>
            body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
            .font-display { font-family: 'Fraunces', Georgia, serif; }
        </style>
    </head>
    <body class="text-ink bg-mist">

        <div class="flex min-h-screen">
            <!-- Formulário -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 sm:px-16 md:px-24 xl:px-32 relative bg-white">
                <div class="absolute top-8 left-8 sm:left-16 md:left-24 xl:left-32">
                    <a href="/" class="flex items-center gap-4">
                        <img src="{{ asset('images/logo.svg') }}" alt="NC5 Logo" class="h-10 w-auto">
                    </a>
                </div>

                <div class="w-full max-w-sm mx-auto mt-24 lg:mt-0 pb-16 lg:pb-0">
                    {{ $slot }}
                </div>

                <div class="absolute bottom-8 left-8 sm:left-16 md:left-24 xl:left-32 text-xs text-slate">
                    &copy; {{ date('Y') }} NC5 Hub Digital.
                </div>
            </div>

            <!-- Arte / Branding -->
            <div class="hidden lg:flex w-1/2 bg-bruceInk relative items-center justify-center overflow-hidden">
                <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-bruce/15 rounded-full blur-[140px] pointer-events-none"></div>
                <div class="absolute -bottom-40 -left-20 w-[500px] h-[500px] bg-bruce/10 rounded-full blur-[130px] pointer-events-none"></div>

                <div class="relative z-10 p-16 max-w-lg flex flex-col justify-center h-full">
                    <div class="inline-flex items-center gap-2 bg-white/10 border border-white/10 text-white/70 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-10 w-max">
                        <span class="w-2 h-2 bg-bruce rounded-full animate-pulse"></span>
                        Acesso Restrito
                    </div>

                    <h2 class="font-display font-bold text-5xl text-white leading-[1.05] tracking-tight mb-4">
                        Painel de Controle <br><em class="not-italic text-bruce">Estratégico</em>.
                    </h2>
                    <p class="text-lg text-white/60 leading-relaxed mb-12">
                        O ecossistema onde marca, mídia e inteligência artificial se encontram para tracionar conversões.
                    </p>

                    <!-- Integração BruceIA -->
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-md">
                        <div class="flex items-center gap-4 mb-4">
                            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="h-12 w-12 drop-shadow-[0_0_15px_rgba(255,122,26,0.4)]">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-bruce">Motor Ativo</p>
                                <p class="text-white font-display font-bold text-xl">Bruce<span class="text-white/60">IA</span> Integrado</p>
                            </div>
                        </div>
                        <p class="text-sm text-white/50">
                            A inteligência artificial da NC5 operando na leitura de dados, otimização de campanhas e suporte avançado.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
