@extends('layouts.public')

@section('title', 'Análise Gratuita com BruceIA · NC5 Hub')

@section('content')

    {{-- ============================================================
         HERO
         ============================================================ --}}
    <section class="relative overflow-hidden bg-[#0A1128] text-white pt-24 lg:pt-32 pb-20 lg:pb-28 border-b border-white/5">
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#FF7A1A]/60 to-transparent pointer-events-none"></div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center">
                <div class="lg:col-span-7">
                    <h1 class="font-display font-extrabold text-5xl lg:text-7xl leading-[1.05] tracking-tight text-white mb-6">
                        Descubra o que <em class="not-italic text-[#FF7A1A]">trava</em> suas conversões.
                    </h1>

                    <p class="text-lg text-white/70 leading-relaxed max-w-xl font-normal mb-8">
                        Conheça o <strong class="text-white font-semibold">BruceIA</strong>. Treinado pelos estrategistas da NC5, ele cruza seus dados com as dores do seu negócio e devolve um parecer premium em minutos.
                    </p>

                    <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm font-semibold text-white/80">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            100% gratuito
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Resultado em ~30 segundos
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Sem cadastro pra ver o laudo
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="relative bg-white/[0.03] border border-white/10 rounded-3xl p-8 lg:p-10">
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-[#FF7A1A] mb-2">Motor de Análise</p>
                                <p class="font-display font-extrabold text-3xl text-white leading-none">Bruce<span class="text-white/40">IA</span></p>
                            </div>
                            <div class="relative w-14 h-14 flex items-center justify-center">
                                <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-12 h-12 animate-bruce-logo relative z-10">
                            </div>
                        </div>

                        <ul class="space-y-4 text-sm text-white/90 font-medium">
                            <li class="flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                Alinhamento de oferta e demanda
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                Identificação de gargalos de conversão
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                Parecer estratégico focado em vendas
                            </li>
                        </ul>

                        <a href="#form" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#FF7A1A] hover:text-white transition-colors">
                            Começar diagnóstico
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         COMO FUNCIONA
         ============================================================ --}}
    <section class="bg-[#08101F] border-b border-white/5 py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">Como funciona</span>
                <h2 class="font-display font-extrabold text-3xl md:text-4xl text-white leading-tight">Três passos até o laudo</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['01', 'Você conta o cenário', 'Escolhe entre site, Instagram ou marca e responde 3–4 campos objetivos sobre o problema real que trava seu faturamento.'],
                    ['02', 'Bruce cruza os dados', 'A IA coleta métricas reais (PageSpeed, seguidores, bio, conteúdo) e cruza com a dor que você descreveu.'],
                    ['03', 'Recebe um parecer', 'Laudo estruturado em 4 blocos com onde você acerta, o que trava suas vendas e o próximo passo estratégico.'],
                ] as $passo)
                    <div class="bg-white/[0.03] border border-white/10 rounded-2xl p-6">
                        <div class="text-[#FF7A1A] font-display font-extrabold text-3xl mb-3">{{ $passo[0] }}</div>
                        <h3 class="text-white font-bold text-lg mb-2">{{ $passo[1] }}</h3>
                        <p class="text-sm text-white/60 leading-relaxed">{{ $passo[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
         FORMULÁRIO
         ============================================================ --}}
    <section id="form" class="relative py-20 lg:py-24 bg-[#050505]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ loading: false, tipo: 'site' }">

            <div class="text-center mb-12 lg:mb-16">
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">Dados Estratégicos</span>
                <h2 class="font-display font-extrabold text-3xl md:text-5xl text-white leading-tight">Onde está doendo?</h2>
                <p class="mt-4 text-white/60 font-medium max-w-xl mx-auto">Forneça o cenário real. A precisão do parecer é direta ao ponto quanto mais honesto for o input.</p>
            </div>

            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/30 text-red-200 px-6 py-4 rounded-2xl mb-8 text-center text-sm font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-200 px-6 py-4 rounded-2xl mb-8">
                    <ul class="list-disc list-inside text-sm space-y-1 font-medium">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="relative bg-[#0F1729] rounded-3xl border border-white/10 overflow-hidden">

                {{-- Loading overlay --}}
                <div x-show="loading" style="display: none;" class="absolute inset-0 z-50 bg-[#0F1729]/95 backdrop-blur-md flex flex-col items-center justify-center px-6">
                    <div class="relative w-24 h-24 mb-8 flex items-center justify-center">
                        <div class="absolute inset-0 border-[3px] border-white/10 rounded-full"></div>
                        <div class="absolute inset-0 border-[3px] border-transparent border-t-[#FF7A1A] rounded-full animate-spin"></div>
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="IA Processando" class="w-12 h-12 relative z-10 animate-bruce-logo">
                    </div>
                    <h3 class="font-display text-2xl font-extrabold text-white tracking-tight text-center">O Bruce está analisando…</h3>
                    <p class="text-sm font-medium text-white/60 mt-2 max-w-xs text-center leading-relaxed">Cruzando seus dados com PageSpeed, Apify e o histórico de gargalos comuns do seu nicho. Aguarde até 30 segundos.</p>
                </div>

                <form action="{{ route('analise.process') }}" method="POST" @submit="loading = true" class="p-8 md:p-12">
                    @csrf

                    {{-- Passo 1: Identificação --}}
                    <div class="mb-10">
                        <h3 class="text-base font-extrabold text-white mb-6 flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#FF7A1A] text-white flex items-center justify-center text-xs font-bold">1</span>
                            Identificação
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Seu Nome</label>
                                <input type="text" name="nome" value="{{ old('nome') }}" required class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="Ex: João Silva">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="(11) 99999-9999">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">E-mail Corporativo</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="voce@suaempresa.com.br">
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-white/10 mb-10 w-full"></div>

                    {{-- Passo 2: Cenário --}}
                    <div class="mb-10">
                        <h3 class="text-base font-extrabold text-white mb-6 flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#FF7A1A] text-white flex items-center justify-center text-xs font-bold">2</span>
                            Cenário de Negócio
                        </h3>

                        <div class="mb-8">
                            <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-3">O que devemos analisar?</label>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                {{-- Site --}}
                                <label class="relative flex cursor-pointer rounded-xl border border-white/10 bg-white/[0.03] p-5 hover:border-white/20 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-[#FF7A1A]/5 has-[:checked]:ring-1 has-[:checked]:ring-[#FF7A1A]">
                                    <input type="radio" name="tipo_analise" value="site" x-model="tipo" class="sr-only">
                                    <div class="flex flex-col gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-white/[0.06] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-sm font-bold text-white">Site / Landing</span>
                                            <span class="block text-xs text-white/50 mt-1 font-medium">Leads e vendas</span>
                                        </div>
                                    </div>
                                </label>

                                {{-- Rede Social --}}
                                <label class="relative flex cursor-pointer rounded-xl border border-white/10 bg-white/[0.03] p-5 hover:border-white/20 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-[#FF7A1A]/5 has-[:checked]:ring-1 has-[:checked]:ring-[#FF7A1A]">
                                    <input type="radio" name="tipo_analise" value="redes_sociais" x-model="tipo" class="sr-only">
                                    <div class="flex flex-col gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-white/[0.06] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5" stroke-width="1.75"/><circle cx="12" cy="12" r="4" stroke-width="1.75"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-sm font-bold text-white">Rede Social</span>
                                            <span class="block text-xs text-white/50 mt-1 font-medium">Instagram / posicionamento</span>
                                        </div>
                                    </div>
                                </label>

                                {{-- Marca --}}
                                <label class="relative flex cursor-pointer rounded-xl border border-white/10 bg-white/[0.03] p-5 hover:border-white/20 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-[#FF7A1A]/5 has-[:checked]:ring-1 has-[:checked]:ring-[#FF7A1A]">
                                    <input type="radio" name="tipo_analise" value="marca" x-model="tipo" class="sr-only">
                                    <div class="flex flex-col gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-white/[0.06] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-sm font-bold text-white">Marca</span>
                                            <span class="block text-xs text-white/50 mt-1 font-medium">Diferencial competitivo</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Campos condicionais: SITE --}}
                        <div x-show="tipo === 'site'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">URL da Página</label>
                                <input type="url" name="url_site" :required="tipo === 'site'" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="https://suaempresa.com.br">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Objetivo principal desta página</label>
                                <select name="objetivo_site" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white">
                                    <option value="Gerar Leads (Formulário/WhatsApp)">Gerar Leads (Formulário/WhatsApp)</option>
                                    <option value="Venda Direta (Checkout/E-commerce)">Venda Direta (Checkout/E-commerce)</option>
                                    <option value="Apresentação Institucional (Autoridade)">Apresentação Institucional (Autoridade)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Qual o maior problema hoje?</label>
                                <select name="dor_site" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white">
                                    <option value="Tenho tráfego (visitas), mas as pessoas não convertem/compram">Tenho tráfego (visitas), mas as pessoas não convertem/compram</option>
                                    <option value="Pouco tráfego, ninguém acessa">Pouco tráfego, ninguém acessa</option>
                                    <option value="O design está ultrapassado e não passa confiança">O design está ultrapassado e não passa confiança</option>
                                </select>
                            </div>
                        </div>

                        {{-- Campos condicionais: REDES SOCIAIS --}}
                        <div x-show="tipo === 'redes_sociais'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Seu @ ou URL do Instagram</label>
                                <input type="text" name="url_social" :required="tipo === 'redes_sociais'" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="https://instagram.com/sua_marca">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Sua Bio atual (copie exatamente)</label>
                                <textarea name="bio_social" rows="2" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="Ex: Especialistas em…"></textarea>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Produto/serviço de maior valor</label>
                                <input type="text" name="produto_social" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="Ex: Consultoria B2B (Ticket 5k)">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Maior desafio no Instagram?</label>
                                <select name="dor_social" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white">
                                    <option value="Atrair seguidores qualificados (público pagante)">Atrair seguidores qualificados (público pagante)</option>
                                    <option value="Converter os seguidores atuais em clientes (não compram)">Converter os seguidores atuais em clientes (não compram)</option>
                                    <option value="Posicionamento amador que afasta clientes premium">Posicionamento amador que afasta clientes premium</option>
                                </select>
                            </div>
                        </div>

                        {{-- Campos condicionais: MARCA --}}
                        <div x-show="tipo === 'marca'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Nome da Marca</label>
                                <input type="text" name="url_marca" :required="tipo === 'marca'" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="Sua Empresa LTDA">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Promessa principal (slogan/pitch)</label>
                                <input type="text" name="promessa_marca" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="Transformamos… em…">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Quem é o cliente ideal?</label>
                                <input type="text" name="publico_marca" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="Ex: Diretores de indústrias…">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Por que compram de você e não do concorrente?</label>
                                <textarea name="diferencial_marca" rows="2" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-0 focus:border-[#FF7A1A] focus:bg-white/[0.06] text-sm font-medium px-4 py-3 transition-colors text-white placeholder-white/30" placeholder="Qual o diferencial real?"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-white/10 flex flex-col items-center gap-3">
                        <button type="submit" class="w-full md:w-auto min-w-[300px] bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-3 transform hover:-translate-y-0.5">
                            Gerar diagnóstico agora
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                        </button>
                        <p class="text-xs text-white/40 font-medium">Ao enviar, você concorda em receber o laudo por e-mail e WhatsApp.</p>
                    </div>
                </form>
            </div>

            {{-- Preview do output --}}
            <div class="mt-14">
                <div class="text-center mb-8">
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-2 block">O que você recebe</span>
                    <h3 class="font-display font-extrabold text-2xl md:text-3xl text-white leading-tight">Estrutura do laudo do Bruce</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([
                        ['Visão Geral', 'Leitura fria e direta do seu cenário, apontando o dedo na dor que você relatou.'],
                        ['Onde você acerta', 'Pontos positivos que já estão puxando resultado — pra você não abandonar o que funciona.'],
                        ['Gargalos', 'O que está travando o faturamento agora, ligado diretamente à dor. Técnico e implacável.'],
                        ['Veredito Estratégico', 'Próximo passo concreto: o que uma agência premium faria pra destravar a operação.'],
                    ] as $bloco)
                        <div class="bg-[#0F1729] border border-white/10 rounded-2xl p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-1.5 h-1.5 bg-[#FF7A1A] rounded-full"></span>
                                <h4 class="text-white font-bold text-sm">{{ $bloco[0] }}</h4>
                            </div>
                            <p class="text-sm text-white/60 leading-relaxed">{{ $bloco[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

@endsection
