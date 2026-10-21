<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-6 mb-2">
            <div>
                <h2 class="font-display font-extrabold text-3xl md:text-4xl text-[#0A1128] leading-tight tracking-tight">
                    Boa {{ now()->hour < 12 ? 'manhã' : (now()->hour < 18 ? 'tarde' : 'noite') }}, {{ Str::before(Auth::user()->name, ' ') }}.
                </h2>
                <p class="text-[#64748B] text-sm mt-2 font-medium">
                    {{ $cliente->razao_social }} · BruceOS · {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('customer.support') }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-[#0A1128] px-5 py-2.5 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    Falar com a NC5
                </a>
                <a href="{{ route('customer.pedidos.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Novo Pedido
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Alerta discreto de contrato pendente (só se houver) --}}
    @if($contratosPendentes > 0)
        <a href="{{ route('customer.contracts') }}" class="mb-8 flex items-center justify-between gap-4 bg-white border border-[#FF7A1A]/40 hover:border-[#FF7A1A] rounded-2xl px-6 py-4 shadow-sm transition-colors group">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-[#FF7A1A] rounded-xl flex items-center justify-center text-white shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-[#0A1128] text-sm">Assinatura de contrato pendente</p>
                    <p class="text-[#64748B] text-xs mt-0.5">Você possui {{ $contratosPendentes }} contrato(s) enviado(s) pela NC5 aguardando assinatura digital.</p>
                </div>
            </div>
            <span class="text-[#FF7A1A] font-bold text-sm group-hover:translate-x-1 transition-transform whitespace-nowrap">Assinar agora &rarr;</span>
        </a>
    @endif

    <!-- KPIs Grid (Padrão Visual Executivo NC5) -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-5 mb-10">
        <!-- Saldo Mensal (Hero Card) -->
        <div class="lg:col-span-2 bg-gradient-to-br from-[#0A1128] to-[#141D38] rounded-2xl p-7 text-white relative overflow-hidden border border-white/10">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-white/[0.06] rounded-xl flex items-center justify-center border border-white/10">
                        <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/70">Saldo Financeiro (Mês)</span>
                </div>
                <h3 class="font-display text-3xl xl:text-4xl font-extrabold text-white tracking-tight mb-2">
                    R$ {{ number_format($saldoMes, 2, ',', '.') }}
                </h3>
                <div class="flex items-center gap-2 mt-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/[0.06] text-white text-[11px] font-semibold border border-white/10">
                        <span class="w-1.5 h-1.5 rounded-full {{ $aVencer7d > 0 ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>
                        {{ $aVencer7d > 0 ? $aVencer7d . ' a vencer (7d)' : 'Contas em dia' }}
                    </span>
                    <a href="{{ route('customer.lancamentos.index') }}" class="text-[11px] font-bold text-white/50 hover:text-white uppercase tracking-wider transition-colors ml-2">Financeiro &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Clientes (CRM) -->
        <a href="{{ route('customer.clientes-finais.index') }}" class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 transition-colors relative overflow-hidden block">
            <span class="absolute inset-y-0 left-0 w-1 bg-emerald-500"></span>
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#64748B] mb-1">Sua Carteira</p>
            <h3 class="font-display text-3xl font-extrabold text-[#0A1128]">{{ number_format($clientesFinaisTotal, 0, ',', '.') }}</h3>
        </a>

        <!-- Pedidos Ativos -->
        <a href="{{ route('customer.pedidos.index') }}" class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 transition-colors relative overflow-hidden block">
            <span class="absolute inset-y-0 left-0 w-1 bg-blue-500"></span>
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#64748B] mb-1">Pedidos Ativos</p>
            <h3 class="font-display text-3xl font-extrabold text-[#0A1128]">{{ $pedidosAtivosCount }}</h3>
        </a>

        <!-- Agenda Hoje -->
        <a href="{{ route('customer.agenda.index') }}" class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 transition-colors relative overflow-hidden block">
            <span class="absolute inset-y-0 left-0 w-1 bg-[#FF7A1A]"></span>
            <div class="w-10 h-10 bg-[#FF7A1A]/10 rounded-xl flex items-center justify-center text-[#FF7A1A] mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#64748B] mb-1">Agenda Hoje</p>
            <h3 class="font-display text-3xl font-extrabold text-[#0A1128]">{{ $compromissosHojeCount }}</h3>
        </a>

        <!-- Comunicações NC5 -->
        <a href="{{ route('customer.materiais') }}" class="bg-white rounded-2xl p-7 border border-gray-100 hover:border-gray-200 transition-colors relative overflow-hidden block">
            <span class="absolute inset-y-0 left-0 w-1 bg-rose-500"></span>
            <div class="flex justify-between items-start mb-5">
                <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                @if($totalPendenciasNc5 > 0)
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                @endif
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-[#64748B] mb-1">Pendências NC5</p>
            <h3 class="font-display text-3xl font-extrabold text-[#0A1128]">{{ $totalPendenciasNc5 }}</h3>
        </a>
    </div>

    <!-- PAINEL CENTRAL EM ABAS: 1) Desenvolver Meu Negócio | 2) Central NC5 (Agência) | 3) Agenda & Bruce IA -->
    <div x-data="{ tab: 'negocio' }" class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header de Navegação das Abas -->
        <div class="border-b border-gray-100 px-8 pt-5 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50">
            <div class="flex gap-8 overflow-x-auto no-scrollbar">
                <button @click="tab = 'negocio'"
                        :class="tab === 'negocio' ? 'text-[#0A1128] border-[#FF7A1A]' : 'text-[#8A8F9C] border-transparent hover:text-[#0A1128]'"
                        class="pb-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Comando do Seu Negócio
                </button>

                <button @click="tab = 'nc5'"
                        :class="tab === 'nc5' ? 'text-[#0A1128] border-[#FF7A1A]' : 'text-[#8A8F9C] border-transparent hover:text-[#0A1128]'"
                        class="pb-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Central NC5 (Comunicações)
                    @if($totalPendenciasNc5 > 0)
                        <span class="bg-[#FF7A1A] text-white py-0.5 px-2 rounded-full text-[10px] font-bold ml-1">{{ $totalPendenciasNc5 }}</span>
                    @else
                        <span class="bg-gray-200 text-gray-600 py-0.5 px-2 rounded-full text-[10px] ml-1">Em dia</span>
                    @endif
                </button>

                <button @click="tab = 'agenda'"
                        :class="tab === 'agenda' ? 'text-[#0A1128] border-[#FF7A1A]' : 'text-[#8A8F9C] border-transparent hover:text-[#0A1128]'"
                        class="pb-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Agenda &amp; Bruce IA
                    @if($compromissosHojeCount + $alertasAtivosTotal > 0)
                        <span class="bg-gray-200 text-gray-700 py-0.5 px-2 rounded-full text-[10px] ml-1">{{ $compromissosHojeCount + $alertasAtivosTotal }}</span>
                    @endif
                </button>
            </div>

            <div class="pb-4">
                <a x-show="tab === 'negocio'" href="{{ route('customer.dashboard-vendas') }}" class="text-[11px] font-bold text-[#8A8F9C] hover:text-[#FF7A1A] uppercase tracking-wider transition-colors inline-flex items-center gap-1">Dashboard de Vendas &rarr;</a>
                <a x-show="tab === 'nc5'" href="{{ route('customer.support') }}" class="text-[11px] font-bold text-[#8A8F9C] hover:text-[#FF7A1A] uppercase tracking-wider transition-colors inline-flex items-center gap-1" style="display: none;">Abrir Chamado NC5 &rarr;</a>
                <a x-show="tab === 'agenda'" href="{{ route('customer.agenda.index') }}" class="text-[11px] font-bold text-[#8A8F9C] hover:text-[#FF7A1A] uppercase tracking-wider transition-colors inline-flex items-center gap-1" style="display: none;">Abrir Agenda Completa &rarr;</a>
            </div>
        </div>

        <!-- =================================================================
             ABA 1: DESENVOLVER MEU NEGÓCIO (Canais de Atração & Atendimento)
             ================================================================= -->
        <div x-show="tab === 'negocio'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-8 space-y-8">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-display font-bold text-xl text-[#0A1128]">Canais de Atração & Atendimento</h3>
                    <p class="text-sm text-[#64748B] mt-0.5">Gerencie seus canais digitais de captação de clientes, presença no Google e atendimento via IA.</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('customer.clientes-finais.create') }}" class="px-4 py-2 rounded-xl border border-gray-200 hover:border-[#0A1128] text-xs font-bold text-[#0A1128] transition-colors">+ Novo Cliente</a>
                    <a href="{{ route('customer.agenda.create') }}" class="px-4 py-2 rounded-xl border border-gray-200 hover:border-[#0A1128] text-xs font-bold text-[#0A1128] transition-colors">+ Agendar</a>
                    <a href="{{ route('customer.pedidos.kanban') }}" class="px-4 py-2 rounded-xl border border-gray-200 hover:border-[#0A1128] text-xs font-bold text-[#0A1128] transition-colors">Kanban de Vendas</a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Card 1: WhatsApp & Bot IA -->
                <div class="rounded-2xl border border-gray-100 bg-gray-50/40 p-7 flex flex-col justify-between hover:border-gray-200 transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/></svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $whatsappConectado ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $whatsappConectado ? 'bg-emerald-500' : 'bg-gray-500' }}"></span>
                                {{ $whatsappConectado ? 'Conectado' : 'Desconectado' }}
                            </span>
                        </div>
                        <h4 class="font-display font-bold text-lg text-[#0A1128]">WhatsApp &amp; Bot IA</h4>
                        <p class="text-sm text-[#64748B] mt-2 leading-relaxed">
                            @if($whatsappConectado)
                                Você tem <strong class="text-[#0A1128]">{{ $whatsappNaoLidas }}</strong> {{ $whatsappNaoLidas === 1 ? 'mensagem não lida' : 'mensagens não lidas' }} em {{ $whatsappConversasTotal }} conversas registradas.
                            @else
                                Conecte seu WhatsApp para responder clientes automaticamente e agendar horários com o Bot IA.
                            @endif
                        </p>
                    </div>
                    <div class="mt-6 pt-5 border-t border-gray-200/70 flex items-center justify-between">
                        <a href="{{ route($whatsappConectado ? 'customer.whatsapp.inbox' : 'customer.whatsapp.conexao') }}" class="text-sm font-bold text-[#0A1128] hover:text-[#FF7A1A] transition-colors">
                            {{ $whatsappConectado ? 'Abrir Inbox' : 'Conectar Agora' }} &rarr;
                        </a>
                        <a href="{{ route('customer.whatsapp.bot.index') }}" class="text-xs font-bold text-[#64748B] hover:text-[#0A1128]">
                            Configurar Bot
                        </a>
                    </div>
                </div>

                <!-- Card 2: Google Meu Negócio -->
                <div class="rounded-2xl border border-gray-100 bg-gray-50/40 p-7 flex flex-col justify-between hover:border-gray-200 transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/></svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $gbpConectado ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-700' }}">
                                {{ $gbpConectado ? 'Integrado' : 'Pendente' }}
                            </span>
                        </div>
                        <h4 class="font-display font-bold text-lg text-[#0A1128]">Google Meu Negócio</h4>
                        @if($gbpConectado && $gbpMetricas)
                            <p class="text-sm text-[#64748B] mt-2 leading-relaxed">
                                <strong class="text-[#0A1128]">{{ number_format($gbpMetricas['atual']['impressoes'] ?? 0, 0, ',', '.') }}</strong> visualizações e
                                <strong class="text-[#0A1128]">{{ number_format($gbpMetricas['atual']['cliques_site'] ?? 0, 0, ',', '.') }}</strong> cliques nos últimos 30 dias.
                            </p>
                        @else
                            <p class="text-sm text-[#64748B] mt-2 leading-relaxed">
                                Edite informações da sua ficha, publique novidades e responda avaliações com ajuda da IA.
                            </p>
                        @endif
                    </div>
                    <div class="mt-6 pt-5 border-t border-gray-200/70 flex items-center justify-between">
                        <a href="{{ route('customer.google-business.index') }}" class="text-sm font-bold text-[#0A1128] hover:text-[#FF7A1A] transition-colors">
                            {{ $gbpConectado ? 'Gerenciar Ficha' : 'Conectar Google' }} &rarr;
                        </a>
                    </div>
                </div>

                <!-- Card 3: Meu Site / Landing Page -->
                <div class="rounded-2xl border border-gray-100 bg-gray-50/40 p-7 flex flex-col justify-between hover:border-gray-200 transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-[#FF7A1A]/10 text-[#FF7A1A] flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ ($landingPage && $landingPage->is_published) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ ($landingPage && $landingPage->is_published) ? 'Publicada' : 'Rascunho' }}
                            </span>
                        </div>
                        <h4 class="font-display font-bold text-lg text-[#0A1128]">Meu Site / Landing Page</h4>
                        <p class="text-sm text-[#64748B] mt-2 leading-relaxed">
                            @if($landingPage)
                                Sua página já registrou <strong class="text-[#0A1128]">{{ number_format($landingPage->leads_count ?? 0, 0, ',', '.') }}</strong> leads e <strong class="text-[#0A1128]">{{ number_format($landingPage->whatsapp_clicks_count ?? 0, 0, ',', '.') }}</strong> cliques no WhatsApp.
                            @else
                                Escolha entre 4 modelos profissionais, personalize cores e publique sua página de vendas.
                            @endif
                        </p>
                    </div>
                    <div class="mt-6 pt-5 border-t border-gray-200/70 flex items-center justify-between">
                        <a href="{{ route('customer.landing-page.edit') }}" class="text-sm font-bold text-[#0A1128] hover:text-[#FF7A1A] transition-colors">
                            {{ $landingPage ? 'Editar Meu Site' : 'Criar Meu Site' }} &rarr;
                        </a>
                        @if($landingPage && $landingPage->is_published)
                            <a href="{{ route('public.landing-page.show', ['slug' => $landingPage->slug]) }}" target="_blank" rel="noopener" class="text-xs font-bold text-[#64748B] hover:text-[#0A1128]">
                                Ver Página &#8599;
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================
             ABA 2: CENTRAL NC5 (Comunicações e Entregas da Agência)
             ================================================================= -->
        <div x-show="tab === 'nc5'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="p-8 space-y-8">

            <!-- 4 Atalhos Diretos da Agência -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <a href="{{ route('customer.materiais') }}" class="p-6 rounded-2xl border {{ $materiaisAguardando > 0 ? 'border-[#FF7A1A] bg-[#FF7A1A]/5' : 'border-gray-100 bg-gray-50/40' }} hover:border-[#0A1128] transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-[#64748B]">Materiais &amp; Artes</span>
                        @if($materiaisAguardando > 0)
                            <span class="px-2.5 py-0.5 rounded-full bg-[#FF7A1A] text-white text-xs font-bold">{{ $materiaisAguardando }}</span>
                        @endif
                    </div>
                    <p class="font-display font-bold text-lg text-[#0A1128]">{{ $materiaisAguardando > 0 ? 'Aprovar materiais' : 'Tudo aprovado' }}</p>
                </a>

                <a href="{{ route('customer.invoices') }}" class="p-6 rounded-2xl border {{ $faturasPendentes > 0 ? 'border-[#FF7A1A] bg-[#FF7A1A]/5' : 'border-gray-100 bg-gray-50/40' }} hover:border-[#0A1128] transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-[#64748B]">Faturas NC5</span>
                        @if($faturasPendentes > 0)
                            <span class="px-2.5 py-0.5 rounded-full bg-[#FF7A1A] text-white text-xs font-bold">{{ $faturasPendentes }}</span>
                        @endif
                    </div>
                    <p class="font-display font-bold text-lg text-[#0A1128]">{{ $faturasPendentes > 0 ? 'Fatura em aberto' : 'Pagamentos em dia' }}</p>
                </a>

                <a href="{{ route('customer.briefings') }}" class="p-6 rounded-2xl border {{ $briefingsPendentes > 0 ? 'border-[#FF7A1A] bg-[#FF7A1A]/5' : 'border-gray-100 bg-gray-50/40' }} hover:border-[#0A1128] transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-[#64748B]">Briefings</span>
                        @if($briefingsPendentes > 0)
                            <span class="px-2.5 py-0.5 rounded-full bg-[#FF7A1A] text-white text-xs font-bold">{{ $briefingsPendentes }}</span>
                        @endif
                    </div>
                    <p class="font-display font-bold text-lg text-[#0A1128]">{{ $briefingsPendentes > 0 ? 'Responder briefing' : 'Em dia' }}</p>
                </a>

                <a href="{{ route('customer.contracts') }}" class="p-6 rounded-2xl border {{ $contratosPendentes > 0 ? 'border-[#FF7A1A] bg-[#FF7A1A]/5' : 'border-gray-100 bg-gray-50/40' }} hover:border-[#0A1128] transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-[#64748B]">Contratos</span>
                        <span class="text-xs font-bold text-[#64748B]">{{ $contratosTotal }}</span>
                    </div>
                    <p class="font-display font-bold text-lg text-[#0A1128]">{{ $contratosPendentes > 0 ? 'Assinatura pendente' : 'Ver contratos' }}</p>
                </a>
            </div>

            <!-- Lista de Entregas Recentes e Fatura -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-display font-bold text-base text-[#0A1128]">Últimos Materiais Enviados pela NC5</h4>
                        <a href="{{ route('customer.materiais') }}" class="text-xs font-bold text-[#FF7A1A] hover:underline">Ver todos</a>
                    </div>
                    @if($ultimosMateriais->isEmpty())
                        <p class="text-sm text-[#64748B] py-6 text-center">Nenhum material enviado recentemente.</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach($ultimosMateriais as $mat)
                                <a href="{{ route('customer.materiais') }}" class="py-3.5 flex items-center justify-between gap-4 hover:bg-gray-50 px-2 rounded-xl transition-colors">
                                    <div>
                                        <p class="font-bold text-sm text-[#0A1128]">{{ $mat->titulo }}</p>
                                        <p class="text-xs text-[#8A8F9C]">Enviado em {{ $mat->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase {{ $mat->status_aprovacao === 'aprovado' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $mat->status_aprovacao }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-display font-bold text-base text-[#0A1128]">Resumo Financeiro NC5</h4>
                            <a href="{{ route('customer.invoices') }}" class="text-xs font-bold text-[#FF7A1A] hover:underline">Minhas faturas</a>
                        </div>
                        @if($proximaFatura)
                            <a href="{{ route('customer.invoices.show', $proximaFatura->id) }}" class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div>
                                    <p class="font-bold text-sm text-[#0A1128]">{{ $proximaFatura->descricao ?? 'Fatura #' . $proximaFatura->id }}</p>
                                    <p class="text-xs text-[#64748B] mt-0.5">Vencimento: {{ $proximaFatura->vencimento ? $proximaFatura->vencimento->format('d/m/Y') : '—' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-display font-bold text-lg text-[#0A1128]">R$ {{ number_format($proximaFatura->valor, 2, ',', '.') }}</p>
                                    <span class="text-[11px] font-bold uppercase {{ $proximaFatura->status === 'pago' ? 'text-emerald-600' : 'text-[#FF7A1A]' }}">{{ $proximaFatura->status }}</span>
                                </div>
                            </a>
                        @else
                            <p class="text-sm text-[#64748B] py-6 text-center">Nenhuma fatura registrada no momento.</p>
                        @endif
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-[#64748B]">
                        <span>Chamados de suporte abertos: <strong class="text-[#0A1128]">{{ $ticketsAbertos }}</strong></span>
                        <a href="{{ route('customer.support') }}" class="font-bold text-[#0A1128] hover:text-[#FF7A1A]">Abrir chamado &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================
             ABA 3: AGENDA & COPILOTO BRUCE IA
             ================================================================= -->
        <div x-show="tab === 'agenda'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Próximos Compromissos -->
                <div class="rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-display font-bold text-base text-[#0A1128]">Próximos Compromissos na Agenda</h4>
                        <a href="{{ route('customer.agenda.create') }}" class="text-xs font-bold text-[#FF7A1A] hover:underline">+ Agendar</a>
                    </div>
                    @if($proximosCompromissos->isEmpty())
                        <p class="text-sm text-[#64748B] py-8 text-center">Nenhum compromisso agendado para as próximas horas.</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach($proximosCompromissos as $apt)
                                <div class="py-3.5 flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-bold text-sm text-[#0A1128]">{{ $apt->titulo }}</p>
                                        <p class="text-xs text-[#64748B]">
                                            {{ $apt->clienteFinal?->nome_responsavel ?: $apt->contato_nome ?: 'Cliente' }}
                                            @if($apt->resource) · {{ $apt->resource->nome }} @endif
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-lg bg-gray-100 text-[#0A1128] font-mono text-xs font-bold">
                                        {{ $apt->scheduled_at->format('d/m H:i') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Alertas do Bruce IA -->
                <div class="rounded-2xl border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="Bruce IA" class="w-7 h-7">
                                <h4 class="font-display font-bold text-base text-[#0A1128]">Alertas e Sugestões do Bruce IA</h4>
                            </div>
                            <a href="{{ route('customer.alertas.index') }}" class="text-xs font-bold text-[#FF7A1A] hover:underline">Ver todos</a>
                        </div>
                        @if($alertasAtivos->isEmpty())
                            <p class="text-sm text-[#64748B] py-8 text-center">Tudo em ordem! Nenhum alerta crítico pendente no momento.</p>
                        @else
                            <div class="divide-y divide-gray-100">
                                @foreach($alertasAtivos as $a)
                                    <a href="{{ route('customer.alertas.index') }}" class="py-3.5 block hover:bg-gray-50 px-2 rounded-xl transition-colors">
                                        <p class="font-bold text-sm text-[#0A1128]">{{ $a->titulo }}</p>
                                        <p class="text-xs text-[#8A8F9C] mt-0.5">{{ $a->created_at->diffForHumans() }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs">
                        <a href="{{ route('customer.agent-drafts.index') }}" class="font-bold text-[#0A1128] hover:text-[#FF7A1A]">
                            Mensagens IA ({{ $rascunhosIaPendentes }})
                        </a>
                        <a href="{{ route('customer.conhecimento.index') }}" class="font-bold text-[#64748B] hover:text-[#0A1128]">
                            Base de Conhecimento &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
