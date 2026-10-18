@extends('layouts.public')

@section('title', 'Contato · Fale com a NC5 Hub')
@section('meta_description', 'Entre em contato com os especialistas da NC5 Hub. Atendimento ágil para acelerar suas vendas, tráfego pago e automação com Inteligência Artificial.')

@section('content')
<main class="min-h-screen bg-[#050505] text-white relative overflow-hidden py-20 lg:py-28" x-data="{ loading: false }">

    {{-- Efeitos de Iluminação / Background Antigravity --}}
    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-[#FF7A1A]/10 blur-[140px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-blue-600/10 blur-[140px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDAuNWg0ME0wLjUgMHY0MCIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiLz48L3N2Zz4=')] z-0 opacity-40 pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">

        {{-- Header da Página --}}
        <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.05] border border-white/10 text-[#FF7A1A] text-xs font-bold uppercase tracking-widest mb-4 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-pulse"></span>
                Atendimento Direto & Especializado
            </span>
            <h1 class="font-sans font-black text-4xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-tight">
                Vamos acelerar o seu <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF7A1A] via-[#FFA866] to-white">negócio?</span>
            </h1>
            <p class="mt-5 text-lg text-white/70 font-normal leading-relaxed">
                Preencha o formulário ou fale diretamente pelos nossos canais oficiais. Nosso time de especialistas responde com agilidade.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            {{-- Formulário Principal --}}
            <div class="lg:col-span-8">
                <div class="relative bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 rounded-3xl p-6 sm:p-10 lg:p-12 shadow-2xl overflow-hidden">
                    
                    {{-- Overlay de Carregamento com BruceIA --}}
                    <div x-show="loading" style="display: none;" class="absolute inset-0 bg-[#0F1729]/95 backdrop-blur-md z-50 flex flex-col items-center justify-center p-6 text-center">
                        <div class="relative w-20 h-20 mb-6 flex items-center justify-center">
                            <div class="absolute inset-0 border-[3px] border-white/10 rounded-full"></div>
                            <div class="absolute inset-0 border-[3px] border-transparent border-t-[#FF7A1A] rounded-full animate-spin"></div>
                            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="NC5 Bruce" class="w-10 h-10 relative z-10">
                        </div>
                        <h3 class="text-xl font-bold text-white tracking-tight">Transmitindo sua mensagem...</h3>
                        <p class="text-sm text-white/60 mt-2 max-w-xs">Nossa central de relacionamento já está recebendo seus dados.</p>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-white tracking-tight">Envie sua mensagem</h2>
                        <p class="text-sm text-white/60 mt-1">Conte um pouco sobre sua empresa e o que você precisa alcançar hoje.</p>
                    </div>

                    @if (session('success'))
                        <div class="mb-8 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 rounded-2xl p-4 sm:p-5 flex items-start gap-3">
                            <svg class="w-6 h-6 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm font-medium leading-relaxed">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-8 bg-rose-500/10 border border-rose-500/30 text-rose-300 rounded-2xl p-4 sm:p-5">
                            <div class="flex items-center gap-2 mb-2 font-bold text-sm text-rose-200">
                                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Por favor, corrija os erros abaixo:
                            </div>
                            <ul class="text-sm space-y-1 list-disc list-inside text-rose-200/90 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contato.store') }}" method="POST" @submit="loading = true" class="space-y-6">
                        @csrf
                        
                        {{-- Honeypot anti-spam --}}
                        <div style="display:none;" aria-hidden="true">
                            <label for="website_url">Website</label>
                            <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nome --}}
                            <div>
                                <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-white/70 mb-2">
                                    Nome Completo <span class="text-[#FF7A1A]">*</span>
                                </label>
                                <input type="text"
                                       name="nome"
                                       id="nome"
                                       required
                                       value="{{ old('nome') }}"
                                       class="w-full bg-white/[0.04] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-white/30 focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] focus:bg-white/[0.07] transition-all outline-none"
                                       placeholder="Seu nome completo">
                            </div>

                            {{-- WhatsApp --}}
                            <div>
                                <label for="whatsapp" class="block text-xs font-bold uppercase tracking-wider text-white/70 mb-2">
                                    WhatsApp (DDD + Número)
                                </label>
                                <input type="text"
                                       name="whatsapp"
                                       id="whatsapp"
                                       value="{{ old('whatsapp') }}"
                                       class="w-full bg-white/[0.04] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-white/30 focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] focus:bg-white/[0.07] transition-all outline-none"
                                       placeholder="(11) 99999-9999">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-white/70 mb-2">
                                    E-mail Corporativo <span class="text-[#FF7A1A]">*</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       required
                                       value="{{ old('email') }}"
                                       class="w-full bg-white/[0.04] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-white/30 focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] focus:bg-white/[0.07] transition-all outline-none"
                                       placeholder="voce@suaempresa.com.br">
                            </div>

                            {{-- Assunto --}}
                            <div>
                                <label for="assunto" class="block text-xs font-bold uppercase tracking-wider text-white/70 mb-2">
                                    Assunto Principal <span class="text-[#FF7A1A]">*</span>
                                </label>
                                <select name="assunto"
                                        id="assunto"
                                        required
                                        class="w-full bg-[#0F1729] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] transition-all outline-none cursor-pointer">
                                    <option value="" disabled {{ old('assunto') ? '' : 'selected' }} class="bg-[#0F1729] text-white/50">Selecione o objetivo</option>
                                    <option value="Quero contratar um serviço" {{ old('assunto') == 'Quero contratar um serviço' ? 'selected' : '' }} class="bg-[#0F1729] text-white">Quero contratar serviços / Gestão</option>
                                    <option value="Tenho dúvidas sobre os planos" {{ old('assunto') == 'Tenho dúvidas sobre os planos' ? 'selected' : '' }} class="bg-[#0F1729] text-white">Planos, Preços e Propostas</option>
                                    <option value="Automação com Bruce IA" {{ old('assunto') == 'Automação com Bruce IA' ? 'selected' : '' }} class="bg-[#0F1729] text-white">Automação de WhatsApp com Bruce IA</option>
                                    <option value="Parceria ou Indicação" {{ old('assunto') == 'Parceria ou Indicação' ? 'selected' : '' }} class="bg-[#0F1729] text-white">Parcerias e Co-produção</option>
                                    <option value="Preciso de suporte" {{ old('assunto') == 'Preciso de suporte' ? 'selected' : '' }} class="bg-[#0F1729] text-white">Já sou cliente · Suporte</option>
                                    <option value="Outro assunto" {{ old('assunto') == 'Outro assunto' ? 'selected' : '' }} class="bg-[#0F1729] text-white">Outro assunto</option>
                                </select>
                            </div>
                        </div>

                        {{-- Mensagem --}}
                        <div>
                            <label for="mensagem" class="block text-xs font-bold uppercase tracking-wider text-white/70 mb-2">
                                Como podemos ajudar seu negócio? <span class="text-[#FF7A1A]">*</span>
                            </label>
                            <textarea name="mensagem"
                                      id="mensagem"
                                      rows="5"
                                      required
                                      class="w-full bg-white/[0.04] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-white/30 focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] focus:bg-white/[0.07] transition-all outline-none resize-none"
                                      placeholder="Conte detalhes sobre o que você precisa: nicho de mercado, metas de vendas, automação ou redesign...">{{ old('mensagem') }}</textarea>
                        </div>

                        {{-- Botão de Envio --}}
                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-[#FF7A1A] hover:bg-[#E5651A] text-white font-bold text-sm px-8 py-4 rounded-xl transition-all shadow-lg shadow-[#FF7A1A]/20 hover:shadow-[#FF7A1A]/40 active:scale-95 cursor-pointer">
                                <span>Enviar mensagem agora</span>
                                <svg class="w-4 h-4 translate-x-0 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                            <p class="text-xs text-white/40 text-center sm:text-right">
                                🔒 Seus dados estão seguros conosco sob a LGPD.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Coluna Lateral: Canais Rápidos & Garantias --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- Card WhatsApp --}}
                <div class="relative group bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-[#25D366]/50 rounded-3xl p-6 sm:p-7 transition-all duration-300 shadow-xl overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-[#25D366]/10 rounded-full blur-2xl group-hover:bg-[#25D366]/20 transition-all"></div>
                    <div class="flex items-start gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-[#25D366]/15 border border-[#25D366]/30 text-[#25D366] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-inner">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.587 1.961.947 3.016.947 3.183 0 5.769-2.586 5.77-5.766.001-3.181-2.585-5.734-5.77-5.734zm0-2.172c4.418 0 8 3.582 8 8 0 4.418-3.582 8-8 8-1.42 0-2.756-.372-3.92-1.019l-4.111 1.077 1.096-4.004c-.694-1.196-1.065-2.574-1.065-4.054 0-4.418 3.582-8 8-8z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#25D366]">Atendimento Imediato</span>
                            <h3 class="font-bold text-lg text-white mt-0.5">WhatsApp Oficial</h3>
                            <p class="text-xs text-white/60 mt-1 leading-relaxed">Converse em tempo real com nossa equipe de atendimento comercial.</p>
                            <a href="https://wa.me/5511999999999?text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20NC5%20Hub%20e%20gostaria%20de%20conversar."
                               target="_blank"
                               class="inline-flex items-center gap-2 text-xs font-bold text-[#25D366] hover:text-white mt-3 transition-colors">
                                Abrir conversa no WhatsApp &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Card E-mail Corporativo --}}
                @php
                    $emailContato = \App\Models\Configuracao::get('mail_reply_to') ?: (\App\Models\Configuracao::get('mail_from_address') ?: 'contato@nc5hub.com.br');
                @endphp
                <div class="relative group bg-[#0F1729]/80 backdrop-blur-xl border border-white/10 hover:border-[#FF7A1A]/50 rounded-3xl p-6 sm:p-7 transition-all duration-300 shadow-xl overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-[#FF7A1A]/10 rounded-full blur-2xl group-hover:bg-[#FF7A1A]/20 transition-all"></div>
                    <div class="flex items-start gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-[#FF7A1A]/15 border border-[#FF7A1A]/30 text-[#FF7A1A] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#FF7A1A]">Canal Institucional</span>
                            <h3 class="font-bold text-lg text-white mt-0.5">E-mail Comercial</h3>
                            <p class="text-xs text-white/60 mt-1 truncate" title="{{ $emailContato }}">{{ $emailContato }}</p>
                            <a href="mailto:{{ $emailContato }}?subject=Contato%20via%20Site%20NC5%20Hub"
                               class="inline-flex items-center gap-2 text-xs font-bold text-[#FF7A1A] hover:text-white mt-3 transition-colors">
                                Enviar e-mail corporativo &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Card Diagnóstico / Bruce IA --}}
                <div class="bg-gradient-to-br from-[#0F1729] to-[#0A0A0B] border border-white/10 rounded-3xl p-6 sm:p-7 shadow-xl">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-[#FF7A1A]/20 border border-[#FF7A1A]/40 flex items-center justify-center text-[#FF7A1A]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-white text-sm">Quer um parecer imediato?</h4>
                    </div>
                    <p class="text-xs text-white/60 leading-relaxed">
                        Experimente nosso motor de IA gratuito para diagnosticar os pontos de atrito no seu site ou redes sociais em 30 segundos.
                    </p>
                    <a href="{{ route('analise.index') }}"
                       class="mt-4 inline-flex items-center justify-center w-full bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white font-bold text-xs px-4 py-3 rounded-xl transition-all">
                        Fazer Diagnóstico Gratuito com BruceIA &rarr;
                    </a>
                </div>

            </div>
        </div>
    </div>
</main>
@endsection
