@extends('layouts.public')

@section('title', 'Análise Gratuita com BruceIA · NC5 Hub')

@section('content')

    {{-- ============================================================
         HERO SECTION · BRUCE IA
         ============================================================ --}}
    <section class="relative overflow-hidden bg-[#0A1128] text-white pt-24 lg:pt-32 pb-20 lg:pb-32">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-[#FF7A1A]/10 rounded-full blur-[150px] pointer-events-none translate-x-1/3 -translate-y-1/3"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-[#FF7A1A]/10 rounded-full blur-[150px] pointer-events-none -translate-x-1/3 translate-y-1/3"></div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center">
                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 px-4 py-2 rounded-full text-[10px] font-bold text-white uppercase tracking-widest backdrop-blur-sm mb-8">
                        <span class="w-1.5 h-1.5 bg-[#FF7A1A] rounded-full animate-pulse"></span>
                        Diagnóstico Inteligente
                    </div>

                    <h1 class="font-display font-extrabold text-5xl lg:text-7xl leading-[1.05] tracking-tight text-white mb-6">
                        Descubra o que <em class="not-italic text-[#FF7A1A]">trava</em> suas conversões.
                    </h1>

                    <p class="text-lg text-white/70 leading-relaxed max-w-xl font-normal mb-8">
                        Conheça o <strong class="text-white font-semibold">BruceIA</strong>. Treinado pelos estrategistas da NC5, ele cruza seus dados com as dores do seu negócio e devolve um parecer premium em minutos.
                    </p>

                    <div class="flex flex-wrap gap-6 text-sm font-semibold text-white/80">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            100% gratuito
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Resultado em 30s
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 relative">
                    <div class="relative bg-white/5 backdrop-blur-md border border-white/10 rounded-[2.5rem] p-10 shadow-2xl overflow-hidden">
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-white/10 rounded-full blur-[50px]"></div>
                        
                        <div class="flex justify-between items-start mb-10">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-[#FF7A1A] mb-2">Motor de Análise</p>
                                <p class="font-display font-extrabold text-3xl text-white leading-none">Bruce<span class="text-white/40">IA</span></p>
                            </div>
                            <div class="relative w-16 h-16 flex items-center justify-center">
                                <div class="absolute inset-0 bg-[#FF7A1A]/30 rounded-full blur-[20px] animate-bruce-aura"></div>
                                <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-12 h-12 animate-bruce-logo relative z-10">
                            </div>
                        </div>

                        <ul class="space-y-4 text-sm text-white/90 font-medium">
                            <li class="flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                Alinhamento de Oferta e Demanda
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                Identificação de gargalos de conversão
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-5 h-5 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                Parecer estratégico focado em vendas
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         FORMULÁRIO ESTRATÉGICO
         ============================================================ --}}
    <section class="relative py-24 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ loading: false, tipo: 'site' }">

            <div class="text-center mb-16">
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3 block">Dados Estratégicos</span>
                <h2 class="font-display font-extrabold text-3xl md:text-5xl text-[#0A1128] leading-tight">Onde está doendo?</h2>
                <p class="mt-4 text-slate-500 font-medium">Forneça o cenário real para a Inteligência Artificial ser precisa no diagnóstico.</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-8 text-center text-sm font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-8">
                    <ul class="list-disc list-inside text-sm space-y-1 font-medium">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="relative bg-white rounded-[2rem] shadow-xl border border-slate-200/60 overflow-hidden">
                
                <!-- Overlay de Loading Premium -->
                <div x-show="loading" style="display: none;" class="absolute inset-0 z-50 bg-white/95 backdrop-blur-md flex flex-col items-center justify-center">
                    <div class="relative w-24 h-24 mb-8 flex items-center justify-center">
                        <div class="absolute inset-0 border-[3px] border-slate-100 rounded-full"></div>
                        <div class="absolute inset-0 border-[3px] border-transparent border-t-[#FF7A1A] rounded-full animate-spin"></div>
                        <div class="absolute inset-0 bg-[#FF7A1A]/10 rounded-full blur-xl animate-pulse"></div>
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="IA Processando" class="w-12 h-12 relative z-10 animate-bruce-logo">
                    </div>
                    <h3 class="font-display text-2xl font-extrabold text-[#0A1128] tracking-tight">O Bruce está analisando...</h3>
                    <p class="text-sm font-medium text-slate-500 mt-2 max-w-xs text-center leading-relaxed">Cruzando seus dados e desafios reais para formular um plano de ação. Aguarde até 30 segundos.</p>
                </div>

                <!-- Formulário -->
                <form action="{{ route('analise.process') }}" method="POST" @submit="loading = true" class="p-8 md:p-12">
                    @csrf

                    <!-- Dados Pessoais -->
                    <div class="mb-12">
                        <h3 class="text-lg font-extrabold text-[#0A1128] mb-6 flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#0A1128] text-white flex items-center justify-center text-xs">1</span>
                            Identificação
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Seu Nome</label>
                                <input type="text" name="nome" value="{{ old('nome') }}" required class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="Ex: João Silva">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="(11) 99999-9999">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">E-mail Corporativo</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="voce@suaempresa.com.br">
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100 mb-12 w-full"></div>

                    <!-- Dados Estratégicos -->
                    <div class="mb-12">
                        <h3 class="text-lg font-extrabold text-[#0A1128] mb-6 flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#0A1128] text-white flex items-center justify-center text-xs">2</span>
                            Cenário de Negócio
                        </h3>
                        
                        <div class="mb-8">
                            <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-3">O que devemos analisar?</label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative flex cursor-pointer rounded-xl border border-slate-200/80 bg-[#F8FAFC] p-4 focus:outline-none hover:border-slate-300 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-orange-50/50 has-[:checked]:ring-1 has-[:checked]:ring-[#FF7A1A]">
                                    <input type="radio" name="tipo_analise" value="site" x-model="tipo" class="sr-only">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-[#0A1128]">Site / Landing Page</span>
                                        <span class="text-xs text-slate-500 mt-1 font-medium">Geração de Leads e Vendas</span>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer rounded-xl border border-slate-200/80 bg-[#F8FAFC] p-4 focus:outline-none hover:border-slate-300 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-orange-50/50 has-[:checked]:ring-1 has-[:checked]:ring-[#FF7A1A]">
                                    <input type="radio" name="tipo_analise" value="redes_sociais" x-model="tipo" class="sr-only">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-[#0A1128]">Rede Social</span>
                                        <span class="text-xs text-slate-500 mt-1 font-medium">Posicionamento e Instagram</span>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer rounded-xl border border-slate-200/80 bg-[#F8FAFC] p-4 focus:outline-none hover:border-slate-300 transition-colors has-[:checked]:border-[#FF7A1A] has-[:checked]:bg-orange-50/50 has-[:checked]:ring-1 has-[:checked]:ring-[#FF7A1A]">
                                    <input type="radio" name="tipo_analise" value="marca" x-model="tipo" class="sr-only">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-[#0A1128]">Marca</span>
                                        <span class="text-xs text-slate-500 mt-1 font-medium">Diferencial competitivo</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Campos Condicionais: SITE -->
                        <div x-show="tipo === 'site'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">URL da Página</label>
                                <input type="url" name="url_site" :required="tipo === 'site'" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="https://suaempresa.com.br">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Objetivo Principal desta Página</label>
                                <select name="objetivo_site" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128]">
                                    <option value="Gerar Leads (Formulário/WhatsApp)">Gerar Leads (Formulário/WhatsApp)</option>
                                    <option value="Venda Direta (Checkout/E-commerce)">Venda Direta (Checkout/E-commerce)</option>
                                    <option value="Apresentação Institucional (Autoridade)">Apresentação Institucional (Autoridade)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Qual o maior problema hoje?</label>
                                <select name="dor_site" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128]">
                                    <option value="Tenho tráfego (visitas), mas as pessoas não convertem/compram">Tenho tráfego (visitas), mas as pessoas não convertem/compram</option>
                                    <option value="Pouco tráfego, ninguém acessa">Pouco tráfego, ninguém acessa</option>
                                    <option value="O design está ultrapassado e não passa confiança">O design está ultrapassado e não passa confiança</option>
                                </select>
                            </div>
                        </div>

                        <!-- Campos Condicionais: REDES SOCIAIS -->
                        <div x-show="tipo === 'redes_sociais'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Seu @ do Instagram</label>
                                <input type="text" name="url_social" :required="tipo === 'redes_sociais'" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="@sua_marca">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Sua Bio atual (copie exatamente como está hoje)</label>
                                <textarea name="bio_social" rows="2" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="Ex: Especialistas em..."></textarea>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Qual produto/serviço de maior valor você vende?</label>
                                <input type="text" name="produto_social" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="Ex: Consultoria B2B (Ticket 5k)">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Qual o seu maior desafio no Instagram?</label>
                                <select name="dor_social" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128]">
                                    <option value="Atrair seguidores qualificados (público pagante)">Atrair seguidores qualificados (público pagante)</option>
                                    <option value="Converter os seguidores atuais em clientes (não compram)">Converter os seguidores atuais em clientes (não compram)</option>
                                    <option value="Posicionamento amador que afasta clientes premium">Posicionamento amador que afasta clientes premium</option>
                                </select>
                            </div>
                        </div>

                        <!-- Campos Condicionais: MARCA -->
                        <div x-show="tipo === 'marca'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Nome da Marca</label>
                                <input type="text" name="url_marca" :required="tipo === 'marca'" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="Sua Empresa LTDA">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Promessa Principal (Slogan/Pitch)</label>
                                <input type="text" name="promessa_marca" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="Transformamos... em...">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Quem é o cliente ideal?</label>
                                <input type="text" name="publico_marca" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="Ex: Diretores de indústrias...">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Por que compram de você e não do concorrente?</label>
                                <textarea name="diferencial_marca" rows="2" class="w-full bg-[#F8FAFC] border-slate-200/80 rounded-xl focus:ring-0 focus:border-[#FF7A1A] text-sm font-medium px-4 py-3 transition-colors text-[#0A1128] placeholder-slate-400" placeholder="Qual o diferencial real?"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex flex-col items-center">
                        <button type="submit" class="w-full md:w-auto min-w-[300px] bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-8 py-4 rounded-xl font-bold text-sm transition-all shadow-lg shadow-[#FF7A1A]/20 flex items-center justify-center gap-3 transform hover:-translate-y-0.5">
                            Gerar diagnóstico agora
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
