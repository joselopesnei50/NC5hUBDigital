@extends('layouts.public')

@section('title', 'Análise Gratuita com BruceIA · NC5 Hub')
@section('meta_description', 'Receba um parecer técnico e estratégico da sua marca, site ou Instagram em menos de 1 minuto com o BruceIA.')

@push('styles')
<style>
    @keyframes arrowFlow {
        0%   { transform: translateX(-6px); opacity: 0.35; }
        50%  { transform: translateX(4px);  opacity: 1; }
        100% { transform: translateX(-6px); opacity: 0.35; }
    }
    @keyframes arrowFlowDown {
        0%   { transform: translateY(-6px); opacity: 0.35; }
        50%  { transform: translateY(4px);  opacity: 1; }
        100% { transform: translateY(-6px); opacity: 0.35; }
    }
    .step-arrow-h { animation: arrowFlow 1.6s ease-in-out infinite; }
    .step-arrow-v { animation: arrowFlowDown 1.6s ease-in-out infinite; }
</style>
@endpush

@section('content')
<main class="min-h-screen bg-[#050505] text-white relative overflow-hidden pb-24 selection:bg-[#FF7A1A] selection:text-white">

    {{-- Efeitos de Iluminação / Background Antigravity --}}
    <div class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-[#FF7A1A]/10 blur-[150px] rounded-full pointer-events-none"></div>
    <div class="absolute top-[35%] right-[-10%] w-[500px] h-[500px] bg-blue-600/10 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[20%] w-[500px] h-[500px] bg-[#FF7A1A]/5 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDAuNWg0ME0wLjUgMHY0MCIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiLz48L3N2Zz4=')] z-0 opacity-40 pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10 pt-20 sm:pt-28">

        {{-- HERO --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center mb-20 lg:mb-28">
            <div class="lg:col-span-7">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.05] border border-white/10 text-[#FF7A1A] text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-pulse"></span>
                    Diagnóstico Estratégico Instantâneo
                </span>
                
                <h1 class="font-display font-black text-4xl sm:text-5xl lg:text-7xl text-white tracking-tight leading-[1.05] mb-6">
                    Descubra o que trava suas conversões.
                </h1>

                <p class="text-lg sm:text-xl text-white/70 leading-relaxed max-w-xl font-normal mb-8">
                    Conheça o <strong class="text-white font-semibold">BruceIA</strong>. Treinado pelos estrategistas da NC5, ele cruza seus dados reais com os gargalos do seu negócio e devolve um parecer premium em menos de 1 minuto.
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
                        Sem necessidade de cartão
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="relative bg-[#0B0F19] border border-white/10 rounded-3xl p-8 lg:p-10 shadow-2xl">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#FF7A1A] mb-1">Motor de Análise</p>
                            <p class="font-display font-extrabold text-3xl text-white leading-none">Bruce<span class="text-white/40">IA</span></p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-10 h-10">
                        </div>
                    </div>

                    <ul class="space-y-4 text-sm text-white/85 font-medium">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Alinhamento de oferta e público pagante
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Identificação de gargalos de conversão (CRO)
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Parecer estratégico pronto para execução
                        </li>
                    </ul>

                    <a href="#form" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#FF7A1A] hover:text-white transition-colors">
                        Começar diagnóstico abaixo
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- COMO FUNCIONA (3 PASSOS) --}}
        <div class="mb-20 lg:mb-28">
            <div class="flex items-center justify-between mb-10 pb-4 border-b border-white/10">
                <h2 class="text-2xl font-display font-bold text-white tracking-tight">
                    Três passos até o laudo estratégico
                </h2>
                <span class="text-xs text-white/50 font-mono uppercase tracking-wider">Como funciona</span>
            </div>

            @php $passos = [
                ['01', 'Você conta o cenário', 'Escolhe entre site, Instagram ou marca e responde 3 campos objetivos sobre o desafio atual.'],
                ['02', 'Bruce cruza os dados', 'A IA audita métricas públicas e cruza com os gargalos comuns do seu segmento.'],
                ['03', 'Recebe um parecer', 'Laudo estruturado com pontos fortes, pontos de atrito e o próximo passo recomendado.'],
            ]; @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                @foreach($passos as $i => $passo)
                    <div class="relative bg-[#0B0F19] border border-white/10 rounded-2xl p-8 flex flex-col justify-between">
                        <div>
                            <div class="text-[#FF7A1A] font-display font-extrabold text-3xl mb-3">{{ $passo[0] }}</div>
                            <h3 class="text-white font-bold text-lg mb-2">{{ $passo[1] }}</h3>
                            <p class="text-sm text-white/60 leading-relaxed font-normal">{{ $passo[2] }}</p>
                        </div>

                        @if($i < count($passos) - 1)
                            {{-- Seta desktop --}}
                            <div class="hidden md:flex absolute top-1/2 -right-5 -translate-y-1/2 w-6 h-6 items-center justify-center pointer-events-none z-10 text-white/30">
                                <svg class="step-arrow-h w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- FORMULÁRIO --}}
        <div id="form" class="max-w-4xl mx-auto" x-data="{ loading: false, tipo: 'redes_sociais' }">

            <div class="text-center mb-12">
                <h2 class="font-display font-black text-3xl sm:text-5xl text-white tracking-tight">Onde está doendo?</h2>
                <p class="mt-4 text-white/65 font-normal max-w-xl mx-auto">Forneça o cenário real. Quanto mais específico for seu relato, mais cirúrgico será o veredito do Bruce.</p>
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

            <div class="relative bg-[#0B0F19] rounded-3xl border border-white/10 overflow-hidden shadow-2xl">

                {{-- Loading overlay --}}
                <div x-show="loading" style="display: none;" class="absolute inset-0 z-50 bg-[#0B0F19]/95 backdrop-blur-md flex flex-col items-center justify-center px-6">
                    <div class="relative w-24 h-24 mb-8 flex items-center justify-center">
                        <div class="absolute inset-0 border-[3px] border-white/10 rounded-full"></div>
                        <div class="absolute inset-0 border-[3px] border-transparent border-t-[#FF7A1A] rounded-full animate-spin"></div>
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="IA Processando" class="w-12 h-12 relative z-10">
                    </div>
                    <h3 class="font-display text-2xl font-extrabold text-white tracking-tight text-center">O Bruce está analisando…</h3>
                    <p class="text-sm font-medium text-white/60 mt-2 max-w-xs text-center leading-relaxed">Cruzando dados de velocidade, posicionamento e gargalos comuns do seu nicho. Aguarde cerca de 30 segundos.</p>
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
                                <input type="text" name="nome" value="{{ old('nome') }}" required class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="Ex: João Silva">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="(11) 99999-9999">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">E-mail Corporativo</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="voce@suaempresa.com.br">
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
                                <label class="relative flex cursor-pointer rounded-xl border border-white/10 bg-white/[0.02] p-5 hover:border-white/20 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-white/5">
                                    <input type="radio" name="tipo_analise" value="site" x-model="tipo" class="sr-only">
                                    <div class="flex flex-col gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-white/[0.06] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-sm font-bold text-white">Site / Landing Page</span>
                                            <span class="block text-xs text-white/50 mt-1 font-medium">Tráfego e conversão</span>
                                        </div>
                                    </div>
                                </label>

                                {{-- Rede Social --}}
                                <label class="relative flex cursor-pointer rounded-xl border border-white/10 bg-white/[0.02] p-5 hover:border-white/20 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-white/5">
                                    <input type="radio" name="tipo_analise" value="redes_sociais" x-model="tipo" checked class="sr-only">
                                    <div class="flex flex-col gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-white/[0.06] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5" stroke-width="1.75"/><circle cx="12" cy="12" r="4" stroke-width="1.75"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-sm font-bold text-white">Rede Social</span>
                                            <span class="block text-xs text-white/50 mt-1 font-medium">Instagram & Perfil</span>
                                        </div>
                                    </div>
                                </label>

                                {{-- Marca --}}
                                <label class="relative flex cursor-pointer rounded-xl border border-white/10 bg-white/[0.02] p-5 hover:border-white/20 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-white/5">
                                    <input type="radio" name="tipo_analise" value="marca" x-model="tipo" class="sr-only">
                                    <div class="flex flex-col gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-white/[0.06] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-sm font-bold text-white">Marca & Oferta</span>
                                            <span class="block text-xs text-white/50 mt-1 font-medium">Diferencial & Posicionamento</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Campos condicionais: SITE --}}
                        <div x-show="tipo === 'site'" x-transition style="display: none;" class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">URL da Página</label>
                                <input type="url" name="url_site" :required="tipo === 'site'" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="https://suaempresa.com.br">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Objetivo principal desta página</label>
                                <select name="objetivo_site" class="w-full bg-[#0B0F19] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white transition-all outline-none">
                                    <option value="Gerar Leads (Formulário/WhatsApp)">Gerar Leads (Formulário/WhatsApp)</option>
                                    <option value="Venda Direta (Checkout/E-commerce)">Venda Direta (Checkout/E-commerce)</option>
                                    <option value="Apresentação Institucional (Autoridade)">Apresentação Institucional (Autoridade)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Qual o maior problema hoje?</label>
                                <select name="dor_site" class="w-full bg-[#0B0F19] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white transition-all outline-none">
                                    <option value="Tenho tráfego (visitas), mas as pessoas não convertem/compram">Tenho tráfego (visitas), mas as pessoas não convertem/compram</option>
                                    <option value="Pouco tráfego, ninguém acessa">Pouco tráfego, ninguém acessa</option>
                                    <option value="O design está ultrapassado e não passa confiança">O design está ultrapassado e não passa confiança</option>
                                </select>
                            </div>
                        </div>

                        {{-- Campos condicionais: REDES SOCIAIS --}}
                        <div x-show="tipo === 'redes_sociais'" x-transition style="display: none;" class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Seu @ ou link do Instagram</label>
                                <input type="text" name="url_social" :required="tipo === 'redes_sociais'" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="https://instagram.com/sua_marca">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Sua Bio atual (copie exatamente)</label>
                                <textarea name="bio_social" rows="2" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="Ex: Especialistas em…"></textarea>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Produto/serviço principal</label>
                                <input type="text" name="produto_social" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="Ex: Consultoria B2B ou Produto X">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Maior desafio no Instagram?</label>
                                <select name="dor_social" class="w-full bg-[#0B0F19] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white transition-all outline-none">
                                    <option value="Atrair seguidores qualificados (público pagante)">Atrair seguidores qualificados (público pagante)</option>
                                    <option value="Converter os seguidores atuais em clientes (não compram)">Converter os seguidores atuais em clientes (não compram)</option>
                                    <option value="Posicionamento amador que afasta clientes premium">Posicionamento amador que afasta clientes premium</option>
                                </select>
                            </div>
                        </div>

                        {{-- Campos condicionais: MARCA --}}
                        <div x-show="tipo === 'marca'" x-transition style="display: none;" class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Nome da Empresa / Marca</label>
                                <input type="text" name="url_marca" :required="tipo === 'marca'" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="Sua Empresa LTDA">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Promessa principal (slogan / proposta de valor)</label>
                                <input type="text" name="promessa_marca" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="Ajudamos empresas X a alcançarem Y">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Quem é o seu cliente ideal?</label>
                                <input type="text" name="publico_marca" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="Ex: Empresários, donos de clínicas, gestores...">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2">Por que compram de você e não do concorrente?</label>
                                <textarea name="diferencial_marca" rows="2" class="w-full bg-white/[0.04] border border-white/10 rounded-xl focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 text-white placeholder-white/30 transition-all outline-none" placeholder="Qual o diferencial real do seu negócio?"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-white/10 flex flex-col items-center gap-3">
                        <button type="submit" class="w-full md:w-auto min-w-[320px] bg-white text-black hover:bg-[#FF7A1A] hover:text-white px-8 py-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-3 shadow-lg cursor-pointer">
                            <span>Gerar Diagnóstico Gratuito</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <p class="text-xs text-white/40 font-medium">Ao enviar, seu parecer será exibido imediatamente na tela.</p>
                    </div>
                </form>
            </div>

            {{-- O que você recebe --}}
            <div class="mt-16">
                <div class="text-center mb-8">
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-white/50 mb-2 block">Estrutura do Parecer</span>
                    <h3 class="font-display font-bold text-2xl text-white">O que você recebe no laudo</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([
                        ['Visão Geral', 'Leitura fria e direta do cenário atual, apontando exatamente o gargalo citado.'],
                        ['Onde Você Acerta', 'Pontos positivos identificados para você manter e alavancar nos seus canais.'],
                        ['Gargalos Críticos', 'Os atritos que estão reduzindo suas conversões e afastando clientes pagantes.'],
                        ['Veredito Estratégico', 'O plano de ação prioritário que nossa equipe executaria para destravar suas vendas.'],
                    ] as $bloco)
                        <div class="bg-[#0B0F19] border border-white/10 rounded-2xl p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-white rounded-full"></span>
                                <h4 class="text-white font-bold text-base">{{ $bloco[0] }}</h4>
                            </div>
                            <p class="text-sm text-white/60 leading-relaxed font-normal">{{ $bloco[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</main>
@endsection
