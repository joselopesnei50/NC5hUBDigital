<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Portal do cliente</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Bem-vindo(a), {{ Str::before(Auth::user()->name, ' ') }}.</h2>
            <p class="text-slate text-sm mt-1">Seu ecossistema com a NC5 Hub · {{ now()->translatedFormat('l, d \d\e F') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- BANNER --}}
        <div class="relative bg-ink rounded-3xl p-8 lg:p-10 text-white overflow-hidden shadow-xl shadow-ink/10">
            <div class="absolute -top-32 -right-24 w-80 h-80 bg-bruce/20 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-3">Sua conta</p>
                    <h1 class="font-display font-bold text-3xl md:text-4xl leading-tight">{{ $cliente->razao_social }}</h1>
                    <p class="text-white/60 mt-3 text-sm max-w-lg">Aqui você acompanha a NC5 no que ela entrega pra você, e usa nossas ferramentas pra tocar seu negócio.</p>
                </div>
                <a href="{{ route('customer.support') }}" class="bg-bruce hover:bg-white hover:text-ink text-white px-6 py-3 rounded-full font-bold text-sm transition-all shadow-lg whitespace-nowrap">
                    Precisa de ajuda?
                </a>
            </div>
        </div>

        {{-- ALERTA: CONTRATOS PENDENTES --}}
        @if($contratosPendentes > 0)
            <a href="{{ route('customer.contracts') }}" class="group block bg-bruce/5 border border-bruce/20 hover:border-bruce/40 rounded-2xl p-5 transition-colors">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-bruce rounded-xl flex items-center justify-center text-white shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-ink text-sm">Ação necessária: assinatura pendente</p>
                            <p class="text-slate text-xs">Você tem {{ $contratosPendentes }} contrato(s) aguardando sua assinatura.</p>
                        </div>
                    </div>
                    <span class="text-bruce font-bold text-sm group-hover:translate-x-1 transition-transform hidden sm:block">Assinar agora →</span>
                </div>
            </a>
        @endif

        {{-- ALERTAS DO BRUCE --}}
        @if($alertasAtivosTotal > 0)
            @php
                $sevMap = [
                    'critico' => ['bg' => 'bg-rose-50',  'border' => 'border-rose-200',  'dot' => 'bg-rose-500',  'label' => 'Crítico', 'labelBg' => 'bg-rose-100 text-rose-700'],
                    'atencao' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Atenção', 'labelBg' => 'bg-amber-100 text-amber-800'],
                    'info'    => ['bg' => 'bg-sky-50',   'border' => 'border-sky-200',   'dot' => 'bg-sky-500',   'label' => 'Info',    'labelBg' => 'bg-sky-100 text-sky-800'],
                ];
            @endphp
            <section class="bg-white border border-black/5 rounded-3xl p-5 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="Bruce" class="w-9 h-9">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-bruce">Alertas do Bruce</p>
                            <p class="font-display font-bold text-ink text-lg leading-tight">{{ $alertasAtivosTotal }} {{ Str::plural('coisa', $alertasAtivosTotal) }} que merecem sua atenção</p>
                        </div>
                    </div>
                    <a href="{{ route('customer.alertas.index') }}" class="text-xs font-bold uppercase tracking-wider text-bruce hover:text-ink transition-colors">
                        Ver todos →
                    </a>
                </div>
                <div class="space-y-2">
                    @foreach($alertasAtivos as $a)
                        @php $s = $sevMap[$a->severidade] ?? $sevMap['info']; @endphp
                        <a href="{{ route('customer.alertas.index') }}" class="block {{ $s['bg'] }} border {{ $s['border'] }} rounded-2xl p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start gap-3">
                                <span class="mt-1.5 w-2 h-2 rounded-full {{ $s['dot'] }} shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="inline-flex text-[10px] font-bold uppercase tracking-wider {{ $s['labelBg'] }} px-1.5 py-0.5 rounded-full">{{ $s['label'] }}</span>
                                        <span class="text-xs text-slate">{{ $a->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="font-display font-bold text-ink text-sm">{{ $a->titulo }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- =========================================================================
             BLOCO 1 · Da NC5 pra você
             O que a agência mandou e o que você precisa devolver pra ela.
             ========================================================================= --}}
        <section>
            <div class="flex items-baseline justify-between gap-3 mb-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-bruce">Da NC5 pra você</p>
                    <h3 class="font-display font-bold text-ink text-xl">O que a agência tem pra você agora</h3>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Faturas pendentes --}}
                <a href="{{ route('customer.invoices') }}"
                   class="block p-5 rounded-2xl border shadow-sm transition-all hover:shadow-md {{ $faturasPendentes > 0 ? 'bg-orange-50 border-orange-200 hover:border-orange-300' : 'bg-white border-black/5' }}">
                    <div class="w-10 h-10 {{ $faturasPendentes > 0 ? 'bg-orange-500 text-white' : 'bg-emerald-50 text-emerald-600' }} rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate">Faturas pendentes</p>
                    <p class="font-display text-3xl font-bold text-ink mt-1">{{ $faturasPendentes }}</p>
                    <p class="text-[11px] mt-2 font-bold uppercase tracking-wider {{ $faturasPendentes > 0 ? 'text-orange-600' : 'text-emerald-600' }}">
                        {{ $faturasPendentes > 0 ? 'Atenção ao vencimento' : 'Tudo em dia' }}
                    </p>
                </a>

                {{-- Materiais aguardando --}}
                <a href="{{ route('customer.materiais') }}"
                   class="block p-5 rounded-2xl border shadow-sm transition-all hover:shadow-md {{ $materiaisAguardando > 0 ? 'bg-bruce/5 border-bruce/20 hover:border-bruce/40' : 'bg-white border-black/5' }}">
                    <div class="w-10 h-10 {{ $materiaisAguardando > 0 ? 'bg-bruce text-white' : 'bg-emerald-50 text-emerald-600' }} rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate">Materiais p/ aprovar</p>
                    <p class="font-display text-3xl font-bold text-ink mt-1">{{ $materiaisAguardando }}</p>
                    <p class="text-[11px] mt-2 font-bold uppercase tracking-wider {{ $materiaisAguardando > 0 ? 'text-bruce' : 'text-emerald-600' }}">
                        {{ $materiaisAguardando > 0 ? 'Aguardando você' : 'Nada pendente' }}
                    </p>
                </a>

                {{-- Briefings novos --}}
                <a href="{{ route('customer.briefings') }}"
                   class="block p-5 rounded-2xl border shadow-sm transition-all hover:shadow-md {{ $briefingsPendentes > 0 ? 'bg-indigo-50 border-indigo-200 hover:border-indigo-300' : 'bg-white border-black/5' }}">
                    <div class="w-10 h-10 {{ $briefingsPendentes > 0 ? 'bg-indigo-600 text-white' : 'bg-emerald-50 text-emerald-600' }} rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate">Briefings novos</p>
                    <p class="font-display text-3xl font-bold text-ink mt-1">{{ $briefingsPendentes }}</p>
                    <p class="text-[11px] mt-2 font-bold uppercase tracking-wider {{ $briefingsPendentes > 0 ? 'text-indigo-600' : 'text-emerald-600' }}">
                        {{ $briefingsPendentes > 0 ? 'Precisa responder' : 'Nada pendente' }}
                    </p>
                </a>

                {{-- Tickets abertos --}}
                <a href="{{ route('customer.support') }}"
                   class="block p-5 rounded-2xl border shadow-sm transition-all hover:shadow-md {{ $ticketsAbertos > 0 ? 'bg-sky-50 border-sky-200 hover:border-sky-300' : 'bg-white border-black/5' }}">
                    <div class="w-10 h-10 {{ $ticketsAbertos > 0 ? 'bg-sky-600 text-white' : 'bg-mist text-slate' }} rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate">Tickets abertos</p>
                    <p class="font-display text-3xl font-bold text-ink mt-1">{{ $ticketsAbertos }}</p>
                    <p class="text-[11px] mt-2 font-bold uppercase tracking-wider {{ $ticketsAbertos > 0 ? 'text-sky-600' : 'text-slate' }}">
                        {{ $ticketsAbertos > 0 ? 'Em andamento' : 'Nenhum aberto' }}
                    </p>
                </a>
            </div>
        </section>

        {{-- =========================================================================
             BLOCO 2 · Seu negócio na plataforma
             O que você tem gerido nas nossas ferramentas.
             ========================================================================= --}}
        <section>
            <div class="flex items-baseline justify-between gap-3 mb-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-bruce">Seu negócio na plataforma</p>
                    <h3 class="font-display font-bold text-ink text-xl">O que você está fazendo com nossas ferramentas</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Financeiro do mês --}}
                <a href="{{ route('customer.lancamentos.index') }}" class="block bg-white border border-black/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Este mês</span>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate">Saldo do mês</p>
                    <p class="font-display text-2xl font-bold {{ $saldoMes >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                        R$ {{ number_format($saldoMes, 2, ',', '.') }}
                    </p>
                    @if($aVencer7d > 0)
                        <p class="text-[11px] text-orange-600 mt-2 font-bold">
                            {{ $aVencer7d }} {{ $aVencer7d === 1 ? 'conta' : 'contas' }} a vencer nos próximos 7 dias
                        </p>
                    @else
                        <p class="text-[11px] text-slate mt-2">Nenhuma conta a vencer em 7 dias</p>
                    @endif
                </a>

                {{-- CRM --}}
                <a href="{{ route('customer.clientes-finais.index') }}" class="block bg-white border border-black/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Sua carteira</span>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate">Clientes cadastrados</p>
                    <p class="font-display text-2xl font-bold text-ink mt-1">{{ number_format($clientesFinaisTotal, 0, ',', '.') }}</p>
                    <p class="text-[11px] text-slate mt-2">
                        Fornecedores cadastrados: <span class="font-bold text-ink">{{ $fornecedoresTotal }}</span>
                    </p>
                </a>

                {{-- Projetos --}}
                <a href="{{ route('customer.projetos.index') }}" class="block bg-white border border-black/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate">Em andamento</span>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate">Projetos ativos</p>
                    <p class="font-display text-2xl font-bold text-ink mt-1">{{ $projetosAtivos }}</p>
                    @if($proximoProjeto)
                        <p class="text-[11px] text-slate mt-2">
                            Próximo prazo: <span class="font-bold text-ink">{{ \Carbon\Carbon::parse($proximoProjeto->data_previsao)->format('d/m/Y') }}</span>
                            — {{ \Illuminate\Support\Str::limit($proximoProjeto->nome, 30) }}
                        </p>
                    @else
                        <p class="text-[11px] text-slate mt-2">Nenhum prazo à vista</p>
                    @endif
                </a>

                {{-- Google Meu Negócio --}}
                <a href="{{ route('customer.google-business.index') }}" class="block bg-white border border-black/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow md:col-span-2">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-bruce/10 text-bruce rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/></svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate">Google Meu Negócio</p>
                                @if(!$gbpConectado)
                                    <p class="font-display text-lg font-bold text-ink">Ainda não conectado</p>
                                @elseif(empty($cliente->google_location_id))
                                    <p class="font-display text-lg font-bold text-ink">Conectado, sem ficha ativa</p>
                                @elseif($gbpMetricas)
                                    @php
                                        $atualImp = $gbpMetricas['atual']['impressoes'] ?? 0;
                                        $delta    = $gbpMetricas['delta']['impressoes'] ?? null;
                                    @endphp
                                    <p class="font-display text-2xl font-bold text-ink">
                                        {{ number_format($atualImp, 0, ',', '.') }}
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate">visualizações · 30d</span>
                                    </p>
                                @else
                                    <p class="font-display text-lg font-bold text-ink">Conectado</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($gbpConectado && !empty($cliente->google_location_id) && $gbpMetricas)
                        @php
                            $delta = $gbpMetricas['delta']['impressoes'] ?? null;
                            if ($delta === null) { $badgeClass = 'bg-emerald-100 text-emerald-700'; $badgeText = 'novo'; }
                            elseif ($delta > 0)  { $badgeClass = 'bg-emerald-100 text-emerald-700'; $badgeText = '+' . number_format($delta, 1, ',', '.') . '%'; }
                            elseif ($delta < 0)  { $badgeClass = 'bg-rose-100 text-rose-700';       $badgeText = number_format($delta, 1, ',', '.') . '%'; }
                            else                 { $badgeClass = 'bg-slate-100 text-slate-600';    $badgeText = '0%'; }
                        @endphp
                        <div class="flex items-center gap-3 text-[11px] flex-wrap">
                            <span class="inline-flex items-center {{ $badgeClass }} font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">
                                {{ $badgeText }} vs 30d anteriores
                            </span>
                            <span class="text-slate">
                                Cliques no site: <span class="font-bold text-ink">{{ number_format($gbpMetricas['atual']['cliques_site'] ?? 0, 0, ',', '.') }}</span>
                            </span>
                            <span class="text-slate">
                                Ligações: <span class="font-bold text-ink">{{ number_format($gbpMetricas['atual']['ligacoes'] ?? 0, 0, ',', '.') }}</span>
                            </span>
                        </div>
                    @else
                        <p class="text-[11px] text-slate mt-1">
                            {{ !$gbpConectado ? 'Conecte sua ficha do Google pra acompanhar visualizações, cliques e avaliações.' : 'Abra o módulo pra ver as métricas atualizadas.' }}
                        </p>
                    @endif
                </a>
            </div>
        </section>

        {{-- AÇÕES RÁPIDAS + DADOS EMPRESA --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 bg-white border border-black/5 rounded-2xl p-6 shadow-sm">
                <h3 class="font-display text-lg font-bold text-ink mb-5">Ações rápidas</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <a href="{{ route('customer.materiais') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-[10px] font-semibold uppercase tracking-wider">Materiais</span>
                        <span class="font-display text-lg font-bold mt-1">Aprovar</span>
                    </a>
                    <a href="{{ route('customer.invoices') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-[10px] font-semibold uppercase tracking-wider">Faturas</span>
                        <span class="font-display text-lg font-bold mt-1">Pagar</span>
                    </a>
                    <a href="{{ route('customer.briefings') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-[10px] font-semibold uppercase tracking-wider">Briefings</span>
                        <span class="font-display text-lg font-bold mt-1">Responder</span>
                    </a>
                    <a href="{{ route('customer.agent-drafts.index') }}" class="group flex flex-col p-5 bg-indigo-50 border border-indigo-100 hover:bg-ink hover:border-transparent hover:text-white rounded-2xl transition-all shadow-sm">
                        <span class="text-indigo-600 group-hover:text-indigo-300 text-[10px] font-semibold uppercase tracking-wider">Mensagens IA</span>
                        <span class="font-display text-lg font-bold mt-1 text-indigo-900 group-hover:text-white">Revisar</span>
                    </a>
                </div>
            </div>

            <div class="bg-white border border-black/5 rounded-2xl p-6 shadow-sm">
                <h3 class="font-display text-lg font-bold text-ink mb-5">Dados da empresa</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-bold text-slate uppercase tracking-wider">Razão social</dt>
                        <dd class="mt-1 text-ink font-semibold">{{ $cliente->razao_social }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate uppercase tracking-wider">Documento ({{ $cliente->tipo_pessoa }})</dt>
                        <dd class="mt-1 text-ink font-semibold">{{ $cliente->cpf_cnpj }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate uppercase tracking-wider">WhatsApp</dt>
                        <dd class="mt-1 text-ink font-semibold">{{ $cliente->telefone ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
