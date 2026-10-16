<x-app-layout>
    <div class="space-y-6">

        {{-- =================================================================
             TOPO EXECUTIVO: Identidade + Status dos Canais + Ações Rápidas
             ================================================================= --}}
        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-7 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 pb-6 border-b border-black/5">
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap mb-1.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-bruce/10 text-bruce">
                            Portal do Cliente
                        </span>
                        <span class="text-xs text-slate font-medium">
                            {{ now()->translatedFormat('l, d \d\e F') }}
                        </span>
                        @if($cliente->cpf_cnpj)
                            <span class="text-xs text-slate/60 font-mono hidden sm:inline">· {{ $cliente->cpf_cnpj }}</span>
                        @endif
                    </div>
                    <h1 class="font-display font-bold text-2xl sm:text-3xl text-ink leading-tight">
                        Olá, {{ Str::before(Auth::user()->name, ' ') }} — <span class="text-bruce">{{ $cliente->razao_social }}</span>
                    </h1>
                    <p class="text-slate text-xs sm:text-sm mt-1">
                        Acompanhe as entregas da agência NC5 e utilize as ferramentas para acelerar as vendas e o atendimento do seu negócio.
                    </p>
                </div>

                {{-- Pills de Status dos Canais em Tempo Real --}}
                <div class="flex items-center gap-2 flex-wrap">
                    {{-- Pill WhatsApp --}}
                    <a href="{{ route($whatsappConectado ? 'customer.whatsapp.inbox' : 'customer.whatsapp.conexao') }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border text-xs font-bold transition-all {{ $whatsappConectado ? 'bg-emerald-50/70 border-emerald-200 text-emerald-800 hover:bg-emerald-100/80' : 'bg-mist border-black/10 text-slate hover:border-ink hover:text-ink' }}">
                        <span class="w-2 h-2 rounded-full {{ $whatsappConectado ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                        <span>WhatsApp {{ $whatsappConectado ? 'Online' : 'Offline' }}</span>
                        @if($whatsappNaoLidas > 0)
                            <span class="px-1.5 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] leading-none">{{ $whatsappNaoLidas }}</span>
                        @endif
                    </a>

                    {{-- Pill Google Meu Negócio --}}
                    <a href="{{ route('customer.google-business.index') }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border text-xs font-bold transition-all {{ $gbpConectado ? 'bg-blue-50/70 border-blue-200 text-blue-800 hover:bg-blue-100/80' : 'bg-mist border-black/10 text-slate hover:border-ink hover:text-ink' }}">
                        <span class="w-2 h-2 rounded-full {{ $gbpConectado ? 'bg-blue-500' : 'bg-slate-400' }}"></span>
                        <span>Google {{ $gbpConectado ? 'Conectado' : 'Conectar' }}</span>
                    </a>

                    {{-- Pill Landing Page --}}
                    <a href="{{ route('customer.landing-page.edit') }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl border text-xs font-bold transition-all {{ ($landingPage && $landingPage->is_published) ? 'bg-indigo-50/70 border-indigo-200 text-indigo-800 hover:bg-indigo-100/80' : 'bg-mist border-black/10 text-slate hover:border-ink hover:text-ink' }}">
                        <span class="w-2 h-2 rounded-full {{ ($landingPage && $landingPage->is_published) ? 'bg-indigo-500' : 'bg-amber-400' }}"></span>
                        <span>Site {{ ($landingPage && $landingPage->is_published) ? 'No Ar' : 'Configurar' }}</span>
                    </a>
                </div>
            </div>

            {{-- Barra de Ações Rápidas para o Negócio (1 Clique) --}}
            <div class="pt-4 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate mr-1">Ações Rápidas:</span>

                    <a href="{{ route('customer.pedidos.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-ink hover:bg-bruce text-white text-xs font-bold transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Novo Orçamento / Pedido
                    </a>

                    <a href="{{ route('customer.agenda.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-mist hover:bg-ink hover:text-white text-ink border border-black/5 text-xs font-bold transition-colors">
                        <svg class="w-3.5 h-3.5 text-bruce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Agendar Compromisso
                    </a>

                    <a href="{{ route('customer.clientes-finais.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-mist hover:bg-ink hover:text-white text-ink border border-black/5 text-xs font-bold transition-colors">
                        <svg class="w-3.5 h-3.5 text-bruce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Cadastrar Cliente
                    </a>

                    <a href="{{ route('customer.lancamentos.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-mist hover:bg-ink hover:text-white text-ink border border-black/5 text-xs font-bold transition-colors">
                        <svg class="w-3.5 h-3.5 text-bruce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Lançar Receita / Despesa
                    </a>
                </div>

                <a href="{{ route('customer.pedidos.kanban') }}"
                   class="inline-flex items-center gap-1 text-xs font-bold text-bruce hover:text-ink transition-colors">
                    Abrir Kanban de Vendas →
                </a>
            </div>
        </div>

        {{-- =================================================================
             GRID PRINCIPAL ASSIMÉTRICO (2 PILARES DE UX)
             - Esquerda (8 col): Ações do Usuário para Desenvolver o Negócio
             - Direita  (4 col): Comunicações e Entregas da NC5
             ================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- =============================================================
                 COLUNA PRINCIPAL (8 COL) · DESENVOLVER MEU NEGÓCIO
                 ============================================================= --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- 1. PULSO OPERACIONAL DO NEGÓCIO (4 KPIs) --}}
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-bruce"></span>
                            <h2 class="font-display font-bold text-lg text-ink">Comando do Seu Negócio</h2>
                        </div>
                        <a href="{{ route('customer.dashboard-vendas') }}" class="text-xs font-bold text-slate hover:text-bruce transition-colors">
                            Ver relatório de vendas →
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5">
                        {{-- KPI 1: Financeiro --}}
                        <a href="{{ route('customer.lancamentos.index') }}"
                           class="group bg-white rounded-2xl p-4 border border-black/5 hover:border-bruce/40 hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Saldo do Mês</span>
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </div>
                                <p class="font-display text-xl font-bold {{ $saldoMes >= 0 ? 'text-ink' : 'text-rose-600' }}">
                                    R$ {{ number_format($saldoMes, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-black/5 flex items-center justify-between text-[11px]">
                                <span class="text-slate">A vencer (7d)</span>
                                <span class="font-bold {{ $aVencer7d > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                    {{ $aVencer7d > 0 ? $aVencer7d . ' conta(s)' : 'Em dia' }}
                                </span>
                            </div>
                        </a>

                        {{-- KPI 2: Pipeline de Vendas / Pedidos --}}
                        <a href="{{ route('customer.pedidos.index') }}"
                           class="group bg-white rounded-2xl p-4 border border-black/5 hover:border-bruce/40 hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Pedidos Ativos</span>
                                    <div class="w-7 h-7 rounded-lg bg-bruce/10 text-bruce flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                </div>
                                <p class="font-display text-xl font-bold text-ink">
                                    {{ $pedidosAtivosCount }} <span class="text-xs font-normal text-slate">{{ $pedidosAtivosCount === 1 ? 'pedido' : 'pedidos' }}</span>
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-black/5 flex items-center justify-between text-[11px]">
                                <span class="text-slate">Em negociação</span>
                                <span class="font-bold text-ink">R$ {{ number_format($pedidosValorPipeline, 2, ',', '.') }}</span>
                            </div>
                        </a>

                        {{-- KPI 3: Carteira CRM --}}
                        <a href="{{ route('customer.clientes-finais.index') }}"
                           class="group bg-white rounded-2xl p-4 border border-black/5 hover:border-bruce/40 hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Clientes (CRM)</span>
                                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                </div>
                                <p class="font-display text-xl font-bold text-ink">
                                    {{ number_format($clientesFinaisTotal, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-black/5 flex items-center justify-between text-[11px]">
                                <span class="text-slate">Novos este mês</span>
                                <span class="font-bold text-emerald-600">+{{ $clientesNovosMes }}</span>
                            </div>
                        </a>

                        {{-- KPI 4: Agenda & Operação --}}
                        <a href="{{ route('customer.agenda.index') }}"
                           class="group bg-white rounded-2xl p-4 border border-black/5 hover:border-bruce/40 hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Agenda Hoje</span>
                                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </div>
                                <p class="font-display text-xl font-bold text-ink">
                                    {{ $compromissosHojeCount }} <span class="text-xs font-normal text-slate">{{ $compromissosHojeCount === 1 ? 'agendado' : 'agendados' }}</span>
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-black/5 flex items-center justify-between text-[11px]">
                                <span class="text-slate">Projetos ativos</span>
                                <span class="font-bold text-ink">{{ $projetosAtivos }}</span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- 2. CANAIS DIGITAIS DE ATRAÇÃO & ATENDIMENTO (3 CARDS DE AÇÃO) --}}
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <h2 class="font-display font-bold text-lg text-ink">Canais de Atração & Atendimento</h2>
                        </div>
                        <span class="text-xs text-slate">Captação de clientes 24/7</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Canal 1: WhatsApp & Bot IA --}}
                        <div class="bg-white rounded-3xl p-5 border border-black/5 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/></svg>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $whatsappConectado ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $whatsappConectado ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                        {{ $whatsappConectado ? 'Conectado' : 'Desconectado' }}
                                    </span>
                                </div>

                                <h3 class="font-display font-bold text-base text-ink">WhatsApp & Bot IA</h3>
                                <p class="text-xs text-slate mt-1">
                                    @if($whatsappConectado)
                                        Atendimento automático ativo. Você possui <strong class="text-ink">{{ $whatsappNaoLidas }}</strong> {{ $whatsappNaoLidas === 1 ? 'mensagem não lida' : 'mensagens não lidas' }} de {{ $whatsappConversasTotal }} conversas.
                                    @else
                                        Conecte seu WhatsApp por QR Code para o Bot IA atender e agendar clientes sozinho.
                                    @endif
                                </p>
                            </div>

                            <div class="mt-5 pt-3.5 border-t border-black/5 flex items-center justify-between gap-2">
                                @if($whatsappConectado)
                                    <a href="{{ route('customer.whatsapp.inbox') }}" class="text-xs font-bold text-emerald-700 hover:underline">
                                        Abrir Inbox {{ $whatsappNaoLidas > 0 ? "({$whatsappNaoLidas})" : '' }} →
                                    </a>
                                    <a href="{{ route('customer.whatsapp.bot.index') }}" class="text-[11px] font-bold text-slate hover:text-ink">
                                        Treinar Bot
                                    </a>
                                @else
                                    <a href="{{ route('customer.whatsapp.conexao') }}" class="text-xs font-bold text-bruce hover:underline">
                                        Conectar WhatsApp →
                                    </a>
                                    <a href="{{ route('customer.whatsapp.bot.index') }}" class="text-[11px] font-bold text-slate hover:text-ink">
                                        Configurar IA
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Canal 2: Google Meu Negócio --}}
                        <div class="bg-white rounded-3xl p-5 border border-black/5 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/></svg>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $gbpConectado ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $gbpConectado ? 'Integrado' : 'Não conectado' }}
                                    </span>
                                </div>

                                <h3 class="font-display font-bold text-base text-ink">Google Meu Negócio</h3>

                                @if($gbpConectado && $gbpMetricas)
                                    <div class="mt-2 grid grid-cols-3 gap-2 bg-mist rounded-xl p-2.5 text-center">
                                        <div>
                                            <p class="font-display font-bold text-sm text-ink">{{ number_format($gbpMetricas['atual']['impressoes'] ?? 0, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-slate">Visitas</p>
                                        </div>
                                        <div>
                                            <p class="font-display font-bold text-sm text-ink">{{ number_format($gbpMetricas['atual']['cliques_site'] ?? 0, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-slate">Cliques</p>
                                        </div>
                                        <div>
                                            <p class="font-display font-bold text-sm text-ink">{{ number_format($gbpMetricas['atual']['ligacoes'] ?? 0, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-slate">Ligações</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs text-slate mt-1">
                                        @if(!$gbpConectado)
                                            Conecte sua ficha para editar horários, publicar novidades e responder avaliações com IA.
                                        @else
                                            Ficha conectada! Acesse para atualizar dados cadastrais, postar fotos e ver métricas.
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <div class="mt-5 pt-3.5 border-t border-black/5 flex items-center justify-between gap-2">
                                <a href="{{ route('customer.google-business.index') }}" class="text-xs font-bold text-bruce hover:underline">
                                    {{ $gbpConectado ? 'Gerenciar Ficha & Posts →' : 'Conectar Conta Google →' }}
                                </a>
                            </div>
                        </div>

                        {{-- Canal 3: Meu Site / Landing Page --}}
                        <div class="bg-white rounded-3xl p-5 border border-black/5 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ ($landingPage && $landingPage->is_published) ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ ($landingPage && $landingPage->is_published) ? 'Publicada' : 'Rascunho' }}
                                    </span>
                                </div>

                                <h3 class="font-display font-bold text-base text-ink">Meu Site / Landing Page</h3>

                                @if($landingPage)
                                    <div class="mt-2 grid grid-cols-2 gap-2 bg-mist rounded-xl p-2.5 text-center">
                                        <div>
                                            <p class="font-display font-bold text-sm text-ink">{{ number_format($landingPage->leads_count ?? 0, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-slate">Leads gerados</p>
                                        </div>
                                        <div>
                                            <p class="font-display font-bold text-sm text-ink">{{ number_format($landingPage->whatsapp_clicks_count ?? 0, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-slate">Cliques WhatsApp</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs text-slate mt-1">
                                        Escolha entre 4 modelos prontos de alta conversão, gere os textos com IA e publique seu site em minutos.
                                    </p>
                                @endif
                            </div>

                            <div class="mt-5 pt-3.5 border-t border-black/5 flex items-center justify-between gap-2">
                                <a href="{{ route('customer.landing-page.edit') }}" class="text-xs font-bold text-bruce hover:underline">
                                    {{ $landingPage ? 'Editar Meu Site →' : 'Criar Landing Page →' }}
                                </a>
                                @if($landingPage && $landingPage->is_published)
                                    <a href="{{ route('public.landing-page.show', ['slug' => $landingPage->slug]) }}" target="_blank" rel="noopener"
                                       class="text-[11px] font-bold text-slate hover:text-ink">
                                        Abrir ↗
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. AGENDA PRÓXIMA & COPILOTO BRUCE IA (LADO A LADO) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Próximos Compromissos da Agenda --}}
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-black/5 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-bruce">Operação Diária</p>
                                    <h3 class="font-display font-bold text-base text-ink">Próximos Compromissos</h3>
                                </div>
                                <a href="{{ route('customer.agenda.index') }}" class="text-xs font-bold text-slate hover:text-bruce">
                                    Ver agenda →
                                </a>
                            </div>

                            @if($proximosCompromissos->isEmpty())
                                <div class="py-6 px-4 rounded-2xl bg-mist/70 text-center">
                                    <p class="text-xs text-slate">Nenhum compromisso agendado para as próximas horas.</p>
                                    <a href="{{ route('customer.agenda.create') }}" class="inline-block mt-2 text-xs font-bold text-bruce hover:underline">
                                        + Criar novo agendamento
                                    </a>
                                </div>
                            @else
                                <div class="divide-y divide-black/5">
                                    @foreach($proximosCompromissos as $apt)
                                        <div class="py-2.5 flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <p class="text-xs font-bold text-ink truncate">{{ $apt->titulo }}</p>
                                                    @if($apt->created_via === 'bot')
                                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold uppercase">Bot WA</span>
                                                    @endif
                                                </div>
                                                <p class="text-[11px] text-slate truncate">
                                                    {{ $apt->clienteFinal?->nome_responsavel ?: $apt->contato_nome ?: 'Cliente' }}
                                                    @if($apt->resource) · {{ $apt->resource->nome }} @endif
                                                </p>
                                            </div>
                                            <span class="shrink-0 px-2.5 py-1 rounded-lg bg-mist text-ink font-mono text-[11px] font-bold">
                                                {{ $apt->scheduled_at->isToday() ? 'Hoje ' . $apt->scheduled_at->format('H:i') : $apt->scheduled_at->format('d/m H:i') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-black/5 flex items-center justify-between text-xs">
                            <span class="text-slate">Catálogo cadastrado</span>
                            <a href="{{ route('customer.produtos.index') }}" class="font-bold text-ink hover:text-bruce">
                                {{ $catalogoTotal }} {{ $catalogoTotal === 1 ? 'item' : 'itens' }} · gerenciar →
                            </a>
                        </div>
                    </div>

                    {{-- Copiloto Bruce IA --}}
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-black/5 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="Bruce IA" class="w-8 h-8">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-bruce">Inteligência Artificial</p>
                                        <h3 class="font-display font-bold text-base text-ink">Copiloto Bruce IA</h3>
                                    </div>
                                </div>
                                <a href="{{ route('customer.alertas.index') }}" class="text-xs font-bold text-slate hover:text-bruce">
                                    Alertas ({{ $alertasAtivosTotal }}) →
                                </a>
                            </div>

                            @if($alertasAtivos->isNotEmpty())
                                @php
                                    $sevMap = [
                                        'critico' => ['dot' => 'bg-rose-500',  'label' => 'Crítico', 'bg' => 'bg-rose-50 text-rose-700'],
                                        'atencao' => ['dot' => 'bg-bruce',     'label' => 'Atenção', 'bg' => 'bg-bruce/10 text-bruce'],
                                        'info'    => ['dot' => 'bg-slate-400', 'label' => 'Info',    'bg' => 'bg-slate-100 text-slate-700'],
                                    ];
                                @endphp
                                <div class="space-y-2">
                                    @foreach($alertasAtivos as $a)
                                        @php $s = $sevMap[$a->severidade] ?? $sevMap['info']; @endphp
                                        <a href="{{ route('customer.alertas.index') }}" class="block p-3 rounded-2xl bg-mist/70 hover:bg-mist transition-colors">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $s['bg'] }}">{{ $s['label'] }}</span>
                                                <span class="text-[10px] text-slate">{{ $a->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-xs font-bold text-ink line-clamp-1">{{ $a->titulo }}</p>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-2xl bg-mist/70 space-y-2">
                                    <p class="text-xs font-bold text-ink">Tudo monitorado pelo Bruce IA ✨</p>
                                    <p class="text-[11px] text-slate">
                                        O Bruce analisa diariamente suas finanças, agenda, clientes inativos e conversas para avisar oportunidades.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-black/5 flex items-center justify-between gap-2 text-xs">
                            <a href="{{ route('customer.agent-drafts.index') }}" class="font-bold text-ink hover:text-bruce">
                                Mensagens IA
                                @if($rascunhosIaPendentes > 0)
                                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-bruce text-white text-[10px]">{{ $rascunhosIaPendentes }}</span>
                                @endif
                            </a>
                            <a href="{{ route('customer.conhecimento.index') }}" class="font-bold text-bruce hover:underline">
                                Base de Conhecimento →
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            {{-- =============================================================
                 COLUNA LATERAL DIREITA (4 COL) · CENTRAL DE COMUNICAÇÕES NC5
                 ============================================================= --}}
            <div class="lg:col-span-4 space-y-5">

                <div class="bg-white border border-black/5 rounded-3xl p-6 shadow-sm">
                    {{-- Cabeçalho da Central NC5 --}}
                    <div class="flex items-start justify-between gap-3 pb-4 border-b border-black/5">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-bruce">Agência & Cliente</p>
                            <h2 class="font-display font-bold text-lg text-ink mt-0.5">Central NC5</h2>
                            <p class="text-xs text-slate">Entregas, aprovações e financeiro com a agência.</p>
                        </div>
                        @if($totalPendenciasNc5 > 0)
                            <span class="px-2.5 py-1 rounded-full bg-bruce text-white text-[11px] font-bold shrink-0">
                                {{ $totalPendenciasNc5 }} {{ $totalPendenciasNc5 === 1 ? 'pendência' : 'pendências' }}
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold shrink-0">
                                Em dia ✓
                            </span>
                        @endif
                    </div>

                    {{-- Alerta Prioritário se houver Contrato Pendente de Assinatura --}}
                    @if($contratosPendentes > 0)
                        <a href="{{ route('customer.contracts') }}"
                           class="mt-4 flex items-center justify-between gap-3 p-3.5 rounded-2xl bg-bruce text-white hover:bg-bruceDark transition-colors shadow-sm">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <div>
                                    <p class="text-xs font-bold leading-tight">{{ $contratosPendentes }} contrato(s) para assinar</p>
                                    <p class="text-[11px] text-white/80">Assinatura digital pendente</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold whitespace-nowrap">Assinar →</span>
                        </a>
                    @endif

                    {{-- Atalhos de Status das 4 Áreas da Agência --}}
                    <div class="mt-4 grid grid-cols-2 gap-2.5">
                        <a href="{{ route('customer.materiais') }}"
                           class="p-3 rounded-2xl border transition-all {{ $materiaisAguardando > 0 ? 'bg-bruce/5 border-bruce/30 hover:border-bruce' : 'bg-mist/60 border-transparent hover:border-black/10' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Materiais</span>
                                @if($materiaisAguardando > 0)
                                    <span class="px-1.5 py-0.5 rounded-full bg-bruce text-white text-[10px] font-bold">{{ $materiaisAguardando }}</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-ink">{{ $materiaisAguardando > 0 ? 'Aprovar artes' : 'Tudo aprovado' }}</p>
                        </a>

                        <a href="{{ route('customer.invoices') }}"
                           class="p-3 rounded-2xl border transition-all {{ $faturasPendentes > 0 ? 'bg-bruce/5 border-bruce/30 hover:border-bruce' : 'bg-mist/60 border-transparent hover:border-black/10' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Faturas NC5</span>
                                @if($faturasPendentes > 0)
                                    <span class="px-1.5 py-0.5 rounded-full bg-bruce text-white text-[10px] font-bold">{{ $faturasPendentes }}</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-ink">{{ $faturasPendentes > 0 ? 'Em aberto' : 'Em dia' }}</p>
                        </a>

                        <a href="{{ route('customer.briefings') }}"
                           class="p-3 rounded-2xl border transition-all {{ $briefingsPendentes > 0 ? 'bg-bruce/5 border-bruce/30 hover:border-bruce' : 'bg-mist/60 border-transparent hover:border-black/10' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Briefings</span>
                                @if($briefingsPendentes > 0)
                                    <span class="px-1.5 py-0.5 rounded-full bg-bruce text-white text-[10px] font-bold">{{ $briefingsPendentes }}</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-ink">{{ $briefingsPendentes > 0 ? 'Responder' : 'Concluídos' }}</p>
                        </a>

                        <a href="{{ route('customer.contracts') }}"
                           class="p-3 rounded-2xl border transition-all {{ $contratosPendentes > 0 ? 'bg-bruce/5 border-bruce/30 hover:border-bruce' : 'bg-mist/60 border-transparent hover:border-black/10' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Contratos</span>
                                <span class="text-[10px] font-bold text-slate">{{ $contratosTotal }}</span>
                            </div>
                            <p class="text-xs font-bold text-ink">{{ $contratosPendentes > 0 ? 'Pendente' : 'Ver contratos' }}</p>
                        </a>
                    </div>

                    {{-- Próxima Fatura da Agência (se existir) --}}
                    @if($proximaFatura)
                        <div class="mt-5 pt-4 border-t border-black/5">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Fatura Recente NC5</span>
                                <a href="{{ route('customer.invoices') }}" class="text-[11px] font-bold text-bruce hover:underline">Ver todas</a>
                            </div>
                            <a href="{{ route('customer.invoices.show', $proximaFatura->id) }}"
                               class="flex items-center justify-between p-3 rounded-2xl bg-mist/70 hover:bg-mist transition-colors">
                                <div>
                                    <p class="text-xs font-bold text-ink">{{ $proximaFatura->descricao ?? 'Fatura #' . $proximaFatura->id }}</p>
                                    <p class="text-[11px] text-slate">
                                        Vencimento: {{ $proximaFatura->vencimento ? $proximaFatura->vencimento->format('d/m/Y') : '—' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-ink">R$ {{ number_format($proximaFatura->valor, 2, ',', '.') }}</p>
                                    <span class="inline-block text-[10px] font-bold uppercase {{ $proximaFatura->status === 'pago' ? 'text-emerald-600' : 'text-bruce' }}">
                                        {{ $proximaFatura->status }}
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endif

                    {{-- Últimos Materiais Enviados pela NC5 --}}
                    <div class="mt-5 pt-4 border-t border-black/5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Últimas Entregas & Artes</span>
                            <a href="{{ route('customer.materiais') }}" class="text-[11px] font-bold text-bruce hover:underline">Ver tudo</a>
                        </div>

                        @if($ultimosMateriais->isEmpty())
                            <p class="text-xs text-slate py-2">Nenhum material enviado recentemente pela agência.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($ultimosMateriais as $mat)
                                    @php
                                        $stColor = match($mat->status_aprovacao) {
                                            'aprovado' => 'bg-emerald-50 text-emerald-700',
                                            'reprovado', 'ajuste' => 'bg-rose-50 text-rose-700',
                                            default => 'bg-amber-50 text-amber-700',
                                        };
                                    @endphp
                                    <a href="{{ route('customer.materiais') }}"
                                       class="flex items-center justify-between gap-2 p-2.5 rounded-xl hover:bg-mist transition-colors">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-ink truncate">{{ $mat->titulo }}</p>
                                            <p class="text-[10px] text-slate">{{ $mat->created_at->format('d/m/Y') }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase shrink-0 {{ $stColor }}">
                                            {{ $mat->status_aprovacao }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Suporte & Tickets com a NC5 --}}
                    <div class="mt-5 pt-4 border-t border-black/5">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs font-bold text-ink">Suporte & Solicitações</p>
                                <p class="text-[11px] text-slate">
                                    {{ $ticketsAbertos > 0 ? $ticketsAbertos . ' chamado(s) em atendimento' : 'Nenhum chamado aberto no momento' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('customer.support') }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-ink hover:bg-bruce text-white text-xs font-bold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            Falar com a Equipe NC5
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
