@extends('layouts.public')

@push('styles')
<style>
    .glass-card {
        background: rgba(20, 20, 25, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .neon-text {
        text-shadow: 0 0 20px rgba(255, 122, 26, 0.5);
    }
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(255, 122, 26, 0.15);
    }
    .hero-gradient {
        background: radial-gradient(circle at top right, rgba(255, 122, 26, 0.15) 0%, transparent 40%),
                    radial-gradient(circle at bottom left, rgba(10, 17, 40, 0.8) 0%, transparent 40%);
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden hero-gradient">
        <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center opacity-[0.05]"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center justify-center p-2 rounded-full bg-white/5 border border-white/10 mb-8 backdrop-blur-sm">
                <img src="{{ asset('images/simbolo.svg') }}" alt="Símbolo NC5" class="h-8 w-auto">
            </div>
            
            <h1 class="text-5xl md:text-7xl font-display font-bold text-white tracking-tight mb-8">
                Automação inteligente e <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF7A1A] to-[#E5651A] neon-text">gestão de marca</span> para empresas.
            </h1>
            
            <p class="text-xl text-white/60 max-w-3xl mx-auto mb-12 font-light leading-relaxed">
                Da nutrição de leads à identidade visual de alta performance, a NC5 HUB DIGITAL acelera o seu crescimento com tecnologia e design estratégico.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
                <a href="#solucoes" class="w-full sm:w-auto bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-full font-bold text-lg transition-all shadow-[0_0_20px_rgba(255,122,26,0.3)] hover:shadow-[0_0_30px_rgba(255,122,26,0.5)] flex items-center justify-center gap-2">
                    Conhecer Soluções
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                </a>
                <a href="{{ route('analise.index') }}" class="w-full sm:w-auto glass-card hover:bg-white/10 text-white px-8 py-4 rounded-full font-bold text-lg transition-all flex items-center justify-center gap-2">
                    Análise Gratuita com IA
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Números / Impacto -->
    <section class="py-12 border-y border-white/5 bg-black/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <h4 class="text-4xl md:text-5xl font-display font-black text-white mb-2">100%</h4>
                    <p class="text-sm font-bold uppercase tracking-widest text-[#FF7A1A]">Foco Estratégico</p>
                </div>
                <div>
                    <h4 class="text-4xl md:text-5xl font-display font-black text-white mb-2">2.5x</h4>
                    <p class="text-sm font-bold uppercase tracking-widest text-[#FF7A1A]">Métrica de Escala</p>
                </div>
                <div>
                    <h4 class="text-4xl md:text-5xl font-display font-black text-white mb-2">12h</h4>
                    <p class="text-sm font-bold uppercase tracking-widest text-[#FF7A1A]">Tempo Recuperado</p>
                </div>
                <div>
                    <h4 class="text-4xl md:text-5xl font-display font-black text-white mb-2">+50</h4>
                    <p class="text-sm font-bold uppercase tracking-widest text-[#FF7A1A]">Sistemas Ativos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção 1: Processo 01 -->
    <section id="solucoes" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-6xl font-display font-black text-white/5 mb-4 block">01</span>
                    <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6">Aceleração <br><span class="text-[#FF7A1A]">Imparável</span></h2>
                    <p class="text-lg text-white/60 mb-8 leading-relaxed">
                        Transforme sua operação em uma máquina de conversão. Desenhamos arquiteturas invisíveis que nutrem leads, eliminam atritos e vendem no piloto automático.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-white/80 font-medium">
                            <span class="w-6 h-6 rounded-full bg-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            Funis de conversão de alta previsibilidade
                        </li>
                        <li class="flex items-center gap-3 text-white/80 font-medium">
                            <span class="w-6 h-6 rounded-full bg-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            Mapeamento e integração total de CRM
                        </li>
                        <li class="flex items-center gap-3 text-white/80 font-medium">
                            <span class="w-6 h-6 rounded-full bg-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            Engenharia de recuperação de receita
                        </li>
                    </ul>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-[#FF7A1A]/20 blur-[100px] rounded-full"></div>
                    <img src="/images/dashboard.png" alt="Visão Estratégica" class="relative z-10 w-full rounded-2xl border border-white/10 shadow-2xl hover-lift">
                </div>
            </div>
        </div>
    </section>

    <!-- Seção 2: Processo 02 -->
    <section class="py-24 bg-black/40 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <div class="order-2 md:order-1 relative">
                    <div class="absolute inset-0 bg-blue-500/10 blur-[100px] rounded-full"></div>
                    <img src="/images/branding.png" alt="Posicionamento de Marca" class="relative z-10 w-full rounded-2xl border border-white/10 shadow-2xl hover-lift">
                </div>
                <div class="order-1 md:order-2">
                    <span class="text-6xl font-display font-black text-white/5 mb-4 block">02</span>
                    <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6">Posicionamento <br><span class="text-[#FF7A1A]">Dominante</span></h2>
                    <p class="text-lg text-white/60 mb-8 leading-relaxed">
                        Não seja apenas mais uma opção; torne-se a escolha óbvia. Esculpimos a presença digital da sua marca com estética visceral e engenharia de tráfego.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-white/80 font-medium">
                            <span class="w-6 h-6 rounded-full bg-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            Direção de Arte e Branding Premium
                        </li>
                        <li class="flex items-center gap-3 text-white/80 font-medium">
                            <span class="w-6 h-6 rounded-full bg-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            Campanhas de tração e performance extrema
                        </li>
                        <li class="flex items-center gap-3 text-white/80 font-medium">
                            <span class="w-6 h-6 rounded-full bg-[#FF7A1A]/20 flex items-center justify-center text-[#FF7A1A]"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            Posicionamento magnético em redes sociais
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção 3: Processo 03 -->
    <section class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-card rounded-3xl p-8 md:p-16 border border-[#FF7A1A]/20 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-tr from-[#FF7A1A]/10 to-transparent pointer-events-none"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center relative z-10">
                    <div>
                        <span class="text-6xl font-display font-black text-white/10 mb-4 block">03</span>
                        <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6">Controle <br>Absoluto</h2>
                        <p class="text-lg text-white/60 mb-8 leading-relaxed">
                            O seu negócio operando sob a sua ótica. Um portal centralizado com inteligência de dados, gestão contratual e suporte tático à distância de um clique.
                        </p>
                        <a href="{{ route('dashboard') }}" class="inline-flex bg-white text-[#0A1128] hover:bg-[#FF7A1A] hover:text-white px-8 py-4 rounded-full font-bold transition-colors">
                            Explore o seu Portal CRM
                        </a>
                    </div>
                    <div>
                        <img src="/images/portal.png" alt="Visão do Portal Dinâmico" class="w-full rounded-xl shadow-2xl hover-lift">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section id="contato" class="py-24 bg-black/60 relative overflow-hidden">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6">O próximo nível exige ação rápida.</h2>
            <p class="text-xl text-white/60 mb-12">Nossa inteligência artificial (Bruce) está pronta para auditar sua empresa agora mesmo. Demora apenas 30 segundos.</p>
            
            <div class="glass-card p-1 rounded-full inline-flex mx-auto">
                <a href="{{ route('analise.index') }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-10 py-5 rounded-full font-bold text-lg transition-all shadow-[0_0_20px_rgba(255,122,26,0.4)] flex items-center gap-3">
                    Iniciar Análise Gratuita
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

@endsection
