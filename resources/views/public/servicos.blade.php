@extends('layouts.public')

@section('title', 'Serviços & Ecossistema 360 · NC5 Hub')

@php
    $whatsapp = '5511999999999';

    $pacotes = [
        [
            'nome'      => 'Presença Profissional',
            'linha'     => 'O mínimo pra sua marca parecer séria.',
            'publico'   => 'Ideal para quem não tem site, tem o Instagram bagunçado ou usa WhatsApp pessoal no trabalho.',
            'itens'     => [
                'Identidade visual básica',
                'Site institucional focado em conversão',
                'Instagram estruturado (bio, destaques)',
                'WhatsApp Business configurado',
                'Google Meu Negócio otimizado',
            ],
            'wa_msg'    => 'Ola! Vi o pacote Presenca Profissional no site da NC5 e quero entender melhor.',
            'color'     => 'from-zinc-800 to-zinc-900',
            'icon'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>'
        ],
        [
            'nome'      => 'Gestão 360 de Redes',
            'linha'     => 'Sua presença rodando todo dia — sem você postar.',
            'publico'   => 'Para quem já vende, quer mais movimento e não tem tempo para pensar em conteúdo.',
            'itens'     => [
                'Planejamento mensal estratégico',
                'Postagens semanais (feed + reels)',
                'Stories diários (oferta e prova social)',
                'Resposta de comentários e DMs',
                'Relatório mensal de resultados',
            ],
            'wa_msg'    => 'Ola! Vi o pacote Gestao 360 de Redes no site da NC5 e quero entender melhor.',
            'color'     => 'from-[#FF7A1A]/20 to-transparent border border-[#FF7A1A]/30',
            'icon'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>'
        ],
        [
            'nome'      => 'Máquina de Vendas',
            'linha'     => 'Cliente entrando todo dia, sem depender de indicação.',
            'publico'   => 'Para quem quer previsibilidade com tráfego pago rodando e funil de WhatsApp automatizado.',
            'itens'     => [
                'Tudo do pacote Gestão 360 de Redes',
                'Tráfego pago (Meta e Google Ads)',
                'Funil de WhatsApp automatizado',
                'CRM para acompanhar cada lead',
                'Análise mensal com Inteligência Artificial',
            ],
            'wa_msg'    => 'Ola! Vi o pacote Maquina de Vendas no site da NC5 e quero entender melhor.',
            'color'     => 'from-blue-900/20 to-transparent border border-blue-500/20',
            'icon'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'
        ],
    ];
@endphp

@section('content')
<div class="bg-[#050505] text-white selection:bg-[#FF7A1A] selection:text-white pb-20">

    {{-- HERO MODERNO --}}
    <section class="relative pt-32 lg:pt-48 pb-20 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[500px] bg-[#FF7A1A] opacity-[0.03] blur-[120px] rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-sm font-medium text-white/70 mb-6">
                    <span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-pulse"></span>
                    Ecossistema de Marketing 360º
                </div>
                <h1 class="font-display font-extrabold text-white leading-[1.05] tracking-tight text-5xl md:text-7xl mb-8">
                    Não fazemos post.<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF7A1A] to-orange-400">
                        Construímos máquinas de venda.
                    </span>
                </h1>
                <p class="text-lg md:text-xl text-white/60 leading-relaxed max-w-2xl">
                    Esqueça agências que só entregam likes. A NC5 Hub atua em todas as pontas do seu negócio para garantir presença, autoridade e conversão.
                </p>
            </div>
        </div>
    </section>

    {{-- BENTO GRID: ECOSSISTEMA 360 --}}
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-white mb-8 tracking-tight">Serviços que desenvolvemos</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 auto-rows-[240px]">
                
                <!-- Bento 1: Gestão de Mídias Sociais -->
                <div class="md:col-span-2 rounded-3xl bg-gradient-to-br from-[#FF7A1A] to-[#E5651A] p-8 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xNSkiLz48L3N2Zz4=')] opacity-50"></div>
                    <div class="relative z-10 flex flex-col h-full justify-center">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                        </div>
                        <h3 class="font-display font-extrabold text-2xl text-white mb-2">Gestão de Mídias Sociais</h3>
                        <p class="text-white/90 text-sm md:text-base leading-relaxed">
                            Conteúdo que gera desejo e autoridade. Transformamos suas redes numa vitrine profissional que atrai clientes todos os dias.
                        </p>
                    </div>
                </div>

                <!-- Bento 2: Automação WhatsApp -->
                <div class="md:col-span-2 rounded-3xl bg-[#0F0F0F] border border-white/10 p-8 relative overflow-hidden group hover:border-white/20 transition-colors">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 blur-[50px] rounded-full pointer-events-none transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10 flex flex-col h-full justify-center">
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-400 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <h3 class="font-display font-bold text-xl md:text-2xl text-white mb-2">Automação para WhatsApp</h3>
                        <p class="text-white/60 text-sm md:text-base">
                            Atendimento rápido, 24/7 e sem bloqueios usando a <strong class="text-white">API Oficial da Meta</strong>.
                        </p>
                    </div>
                </div>

                <!-- Bento 3: Desenvolvimento Web -->
                <div class="md:col-span-2 rounded-3xl bg-[#0F0F0F] border border-white/10 p-8 relative overflow-hidden group hover:border-white/20 transition-colors">
                    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-blue-500/10 blur-[50px] rounded-full pointer-events-none transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10 flex flex-col h-full justify-center">
                        <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-400 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                        <h3 class="font-display font-bold text-xl md:text-2xl text-white mb-2">Desenvolvimento de Website</h3>
                        <p class="text-white/60 text-sm md:text-base">
                            Páginas institucionais, e-commerces e Landing Pages rápidas, modernas e totalmente focadas em gerar conversão.
                        </p>
                    </div>
                </div>

                <!-- Bento 4: Implementação CRM -->
                <div class="md:col-span-1 rounded-3xl bg-[#0F0F0F] border border-white/10 p-6 md:p-8 flex flex-col justify-center relative overflow-hidden hover:border-white/20 transition-colors">
                    <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-400 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-white mb-1">CRM PME</h3>
                        <p class="text-sm text-white/50">Implementação do <strong class="text-white/80">VivensiApp</strong> para organizar vendas e leads.</p>
                    </div>
                </div>

                <!-- Bento 5: Criação de Marca -->
                <div class="md:col-span-1 rounded-3xl bg-[#0F0F0F] border border-white/10 p-6 md:p-8 flex flex-col justify-center relative overflow-hidden hover:border-white/20 transition-colors">
                    <div class="w-10 h-10 bg-pink-500/10 rounded-xl flex items-center justify-center text-pink-400 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-white mb-1">Criação de Marca</h3>
                        <p class="text-sm text-white/50">Identidade visual e logotipos profissionais que destacam o seu negócio.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- FORMULÁRIO DE CONTATO --}}
    <section class="py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="font-display font-extrabold text-3xl md:text-5xl text-white mb-6">Fale com nossa equipe</h2>
                <p class="text-white/60 text-lg">
                    Preencha o formulário abaixo e entraremos em contato para entender o seu negócio.
                </p>
            </div>

            <div class="bg-[#0F0F0F] border border-white/10 rounded-3xl p-8 md:p-12 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-[#FF7A1A]/10 blur-[50px] rounded-full pointer-events-none"></div>
                
                @if(session('success'))
                    <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 font-medium text-center">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('contato.store') }}" method="POST" class="space-y-6 relative z-10">
                    @csrf
                    
                    {{-- Honeypot field (hidden from real users, filled by spam bots) --}}
                    <div style="display:none;" aria-hidden="true">
                        <label for="website_url">Website</label>
                        <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nome" class="block text-sm font-semibold text-white/80 mb-2">Seu Nome</label>
                            <input type="text" name="nome" id="nome" required value="{{ old('nome') }}"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:border-[#FF7A1A] focus:ring-[#FF7A1A] transition-colors"
                                   placeholder="Como prefere ser chamado?">
                            @error('nome') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="whatsapp" class="block text-sm font-semibold text-white/80 mb-2">WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" required value="{{ old('whatsapp') }}"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:border-[#FF7A1A] focus:ring-[#FF7A1A] transition-colors"
                                   placeholder="(00) 00000-0000">
                            @error('whatsapp') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-white/80 mb-2">E-mail</label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:border-[#FF7A1A] focus:ring-[#FF7A1A] transition-colors"
                                   placeholder="seu@email.com">
                            @error('email') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="assunto" class="block text-sm font-semibold text-white/80 mb-2">Assunto</label>
                            <select name="assunto" id="assunto" required
                                    class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#FF7A1A] focus:ring-[#FF7A1A] transition-colors">
                                <option value="">Selecione o serviço desejado...</option>
                                <option value="Gestão de Mídias Sociais" @selected(old('assunto') == 'Gestão de Mídias Sociais')>Gestão de Mídias Sociais</option>
                                <option value="Automação WhatsApp" @selected(old('assunto') == 'Automação WhatsApp')>Automação para WhatsApp</option>
                                <option value="Desenvolvimento de Website" @selected(old('assunto') == 'Desenvolvimento de Website')>Desenvolvimento de Website</option>
                                <option value="Implementação CRM" @selected(old('assunto') == 'Implementação CRM')>Implementação CRM VivensiApp</option>
                                <option value="Criação de Marca" @selected(old('assunto') == 'Criação de Marca')>Criação de Marca</option>
                                <option value="Outros" @selected(old('assunto') == 'Outros')>Outros assuntos</option>
                            </select>
                            @error('assunto') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="mensagem" class="block text-sm font-semibold text-white/80 mb-2">Mensagem</label>
                        <textarea name="mensagem" id="mensagem" rows="4" required
                                  class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:border-[#FF7A1A] focus:ring-[#FF7A1A] transition-colors"
                                  placeholder="Conte-nos um pouco sobre a sua empresa e seus desafios atuais...">{{ old('mensagem') }}</textarea>
                        @error('mensagem') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 text-center">
                        <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-10 py-4 rounded-xl font-bold transition-colors shadow-lg">
                            Enviar Mensagem
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- CTA final --}}
    <section class="py-24 border-t border-white/10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-display font-extrabold text-white text-4xl md:text-5xl mb-6">
                Pronto para acelerar?
            </h2>
            <p class="text-xl text-white/50 mb-10">
                Pare de perder tempo com agências que não entendem do seu negócio. Vamos conversar sobre resultados reais.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('analise.index') }}"
                   class="inline-flex items-center justify-center gap-2 bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-xl text-sm font-bold transition-colors">
                    Fazer análise gratuita
                </a>
                <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Ola! Vim pelo site da NC5 e quero conversar sobre marketing.') }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white px-8 py-4 rounded-xl text-sm font-bold transition-colors border border-white/10">
                    Chamar no WhatsApp
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
