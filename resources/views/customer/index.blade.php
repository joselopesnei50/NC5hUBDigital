<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Portal do cliente</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Bem-vindo(a), {{ Str::before(Auth::user()->name, ' ') }}.</h2>
            <p class="text-slate text-sm mt-1">{{ now()->translatedFormat('l, d \d\e F') }}</p>
        </div>
    </x-slot>

    <div class="space-y-8">

        {{-- BANNER --}}
        <div class="relative bg-ink rounded-3xl p-8 lg:p-10 text-white overflow-hidden">
            <div class="absolute -top-32 -right-24 w-80 h-80 bg-bruce/15 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <p class="text-[10px] font-bold text-bruce uppercase tracking-[0.2em] mb-3">Sua conta</p>
                    <h1 class="font-display font-bold text-3xl md:text-4xl leading-tight">{{ $cliente->razao_social }}</h1>
                    <p class="text-white/60 mt-3 text-sm max-w-lg">Aqui você acompanha a NC5 no que ela entrega pra você, e usa nossas ferramentas pra tocar seu negócio.</p>
                </div>
                <a href="{{ route('customer.support') }}" class="bg-white/10 hover:bg-bruce border border-white/15 hover:border-bruce text-white px-5 py-2.5 rounded-full font-bold text-sm transition-all whitespace-nowrap">
                    Precisa de ajuda?
                </a>
            </div>
        </div>

        {{-- CONTRATOS PENDENTES (só se houver) --}}
        @if($contratosPendentes > 0)
            <a href="{{ route('customer.contracts') }}" class="group block bg-white border border-bruce/30 hover:border-bruce rounded-2xl p-5 transition-colors">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-bruce rounded-2xl flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-ink text-sm">Ação necessária · assinatura pendente</p>
                            <p class="text-slate text-xs mt-0.5">Você tem {{ $contratosPendentes }} contrato(s) aguardando sua assinatura.</p>
                        </div>
                    </div>
                    <span class="text-bruce font-bold text-sm group-hover:translate-x-1 transition-transform hidden sm:block">Assinar →</span>
                </div>
            </a>
        @endif

        {{-- ALERTAS DO BRUCE (só se houver) --}}
        @if($alertasAtivosTotal > 0)
            @php
                $sevMap = [
                    'critico' => ['dot' => 'bg-rose-500',  'label' => 'Crítico', 'labelBg' => 'bg-rose-50 text-rose-700 border-rose-100'],
                    'atencao' => ['dot' => 'bg-bruce',     'label' => 'Atenção', 'labelBg' => 'bg-bruce/10 text-bruce border-bruce/20'],
                    'info'    => ['dot' => 'bg-slate-400', 'label' => 'Info',    'labelBg' => 'bg-slate-50 text-slate-700 border-slate-200'],
                ];
            @endphp
            <section class="bg-white border border-black/5 rounded-3xl p-6 sm:p-7">
                <div class="flex items-center justify-between gap-3 mb-5 flex-wrap">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="Bruce" class="w-10 h-10">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-bruce">Bruce IA</p>
                            <p class="font-display font-bold text-ink text-lg leading-tight">{{ $alertasAtivosTotal }} {{ Str::plural('item', $alertasAtivosTotal) }} pra você olhar</p>
                        </div>
                    </div>
                    <a href="{{ route('customer.alertas.index') }}" class="text-xs font-bold uppercase tracking-wider text-slate hover:text-bruce transition-colors">
                        Ver todos →
                    </a>
                </div>
                <div class="divide-y divide-black/5">
                    @foreach($alertasAtivos as $a)
                        @php $s = $sevMap[$a->severidade] ?? $sevMap['info']; @endphp
                        <a href="{{ route('customer.alertas.index') }}" class="flex items-start gap-4 py-3 hover:pl-2 transition-all group">
                            <span class="mt-2 w-2 h-2 rounded-full {{ $s['dot'] }} shrink-0"></span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="inline-flex text-[10px] font-bold uppercase tracking-wider {{ $s['labelBg'] }} border px-2 py-0.5 rounded-full">{{ $s['label'] }}</span>
                                    <span class="text-xs text-slate">{{ $a->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="font-display font-bold text-ink text-sm group-hover:text-bruce transition-colors">{{ $a->titulo }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- =================================================================
             BLOCO 1 · Da NC5 pra você
             ================================================================= --}}
        <section>
            <div class="flex items-baseline gap-4 mb-5">
                <span class="font-display text-4xl font-bold text-bruce/20 leading-none">01</span>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-bruce">Da NC5 pra você</p>
                    <h3 class="font-display font-bold text-ink text-2xl leading-tight">O que a agência tem pra você agora</h3>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @php
                    $nc5Cards = [
                        ['route' => 'customer.invoices',   'label' => 'Faturas pendentes',  'valor' => $faturasPendentes,    'acao' => 'Pagar',      'iconPath' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['route' => 'customer.materiais',  'label' => 'Materiais',           'valor' => $materiaisAguardando, 'acao' => 'Aprovar',    'iconPath' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['route' => 'customer.briefings',  'label' => 'Briefings',           'valor' => $briefingsPendentes,  'acao' => 'Responder',  'iconPath' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['route' => 'customer.support',    'label' => 'Tickets abertos',    'valor' => $ticketsAbertos,      'acao' => 'Acompanhar', 'iconPath' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
                    ];
                @endphp

                @foreach($nc5Cards as $c)
                    @php $ativo = $c['valor'] > 0; @endphp
                    <a href="{{ route($c['route']) }}"
                       class="relative block p-5 rounded-2xl border transition-all
                              {{ $ativo ? 'bg-bruce/5 border-bruce/20 hover:border-bruce hover:shadow-md' : 'bg-white border-black/5 hover:border-black/10' }}">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center
                                        {{ $ativo ? 'bg-bruce text-white' : 'bg-mist text-slate' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['iconPath'] }}"/></svg>
                            </div>
                            @if($ativo)
                                <span class="inline-flex items-center justify-center min-w-[24px] h-6 px-2 bg-bruce text-white text-xs font-bold rounded-full">
                                    {{ $c['valor'] }}
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate">{{ $c['label'] }}</p>
                        <p class="font-display text-lg font-bold text-ink mt-0.5">
                            {{ $ativo ? $c['acao'] : 'Nada pendente' }}
                        </p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- =================================================================
             BLOCO 2 · Seu negócio na plataforma
             ================================================================= --}}
        <section>
            <div class="flex items-baseline gap-4 mb-5">
                <span class="font-display text-4xl font-bold text-bruce/20 leading-none">02</span>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-bruce">Seu negócio na plataforma</p>
                    <h3 class="font-display font-bold text-ink text-2xl leading-tight">O que você está fazendo com nossas ferramentas</h3>
                </div>
            </div>

            {{-- Google Meu Negócio (full width, protagonista) --}}
            <a href="{{ route('customer.google-business.index') }}" class="block bg-ink text-white rounded-3xl p-7 mb-3 hover:bg-ink/95 transition-colors relative overflow-hidden">
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-bruce/10 rounded-full blur-[80px] pointer-events-none"></div>
                <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-bruce">Google Meu Negócio</p>
                            @if(!$gbpConectado)
                                <p class="font-display text-xl font-bold mt-1">Conecte sua ficha do Google</p>
                                <p class="text-white/60 text-xs mt-1">Veja visualizações, cliques, avaliações e responda tudo por aqui.</p>
                            @elseif(empty($cliente->google_location_id))
                                <p class="font-display text-xl font-bold mt-1">Escolha uma ficha</p>
                                <p class="text-white/60 text-xs mt-1">Conectado — falta só selecionar qual ficha gerenciar.</p>
                            @elseif($gbpMetricas)
                                <p class="font-display text-3xl font-bold mt-1">
                                    {{ number_format($gbpMetricas['atual']['impressoes'] ?? 0, 0, ',', '.') }}
                                </p>
                                <p class="text-white/60 text-xs mt-0.5">visualizações nos últimos 30 dias</p>
                            @else
                                <p class="font-display text-xl font-bold mt-1">Métricas atualizadas em breve</p>
                                <p class="text-white/60 text-xs mt-1">Abra o módulo pra carregar os dados.</p>
                            @endif
                        </div>
                    </div>

                    @if($gbpConectado && !empty($cliente->google_location_id) && $gbpMetricas)
                        @php
                            $delta = $gbpMetricas['delta']['impressoes'] ?? null;
                            if ($delta === null)     { $bClass = 'bg-emerald-500/20 text-emerald-300'; $bTxt = 'novo'; }
                            elseif ($delta > 0)      { $bClass = 'bg-emerald-500/20 text-emerald-300'; $bTxt = '+' . number_format($delta, 1, ',', '.') . '%'; }
                            elseif ($delta < 0)      { $bClass = 'bg-rose-500/20 text-rose-300';       $bTxt = number_format($delta, 1, ',', '.') . '%'; }
                            else                     { $bClass = 'bg-white/10 text-white/60';         $bTxt = '0%'; }
                        @endphp
                        <div class="grid grid-cols-3 gap-5 md:min-w-[300px]">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-white/50">vs 30d ant.</p>
                                <p class="mt-1 inline-flex items-center {{ $bClass }} px-2 py-0.5 rounded-full text-xs font-bold">{{ $bTxt }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-white/50">Cliques</p>
                                <p class="font-display text-lg font-bold mt-1">{{ number_format($gbpMetricas['atual']['cliques_site'] ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-white/50">Ligações</p>
                                <p class="font-display text-lg font-bold mt-1">{{ number_format($gbpMetricas['atual']['ligacoes'] ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @else
                        <span class="inline-flex items-center gap-2 bg-bruce hover:bg-bruceDark px-5 py-2.5 rounded-full font-bold text-sm text-white transition-colors self-start md:self-auto">
                            {{ !$gbpConectado ? 'Conectar agora' : 'Abrir módulo' }} →
                        </span>
                    @endif
                </div>
            </a>

            {{-- 3 cards secundários --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                {{-- Financeiro --}}
                <a href="{{ route('customer.lancamentos.index') }}" class="block bg-white rounded-2xl p-5 border border-black/5 hover:border-black/10 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate">Financeiro</p>
                        <span class="text-[10px] font-bold text-slate">Este mês</span>
                    </div>
                    <p class="font-display text-2xl font-bold {{ $saldoMes >= 0 ? 'text-ink' : 'text-rose-600' }}">
                        R$ {{ number_format($saldoMes, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-slate mt-1">saldo do mês</p>
                    <div class="mt-4 pt-4 border-t border-black/5 flex items-center justify-between text-xs">
                        <span class="text-slate">A vencer em 7d</span>
                        <span class="font-bold {{ $aVencer7d > 0 ? 'text-bruce' : 'text-ink' }}">
                            {{ $aVencer7d }} {{ $aVencer7d === 1 ? 'conta' : 'contas' }}
                        </span>
                    </div>
                </a>

                {{-- Carteira --}}
                <a href="{{ route('customer.clientes-finais.index') }}" class="block bg-white rounded-2xl p-5 border border-black/5 hover:border-black/10 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate">Sua carteira</p>
                    </div>
                    <p class="font-display text-2xl font-bold text-ink">{{ number_format($clientesFinaisTotal, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate mt-1">clientes cadastrados</p>
                    <div class="mt-4 pt-4 border-t border-black/5 flex items-center justify-between text-xs">
                        <span class="text-slate">Fornecedores</span>
                        <span class="font-bold text-ink">{{ number_format($fornecedoresTotal, 0, ',', '.') }}</span>
                    </div>
                </a>

                {{-- Projetos --}}
                <a href="{{ route('customer.projetos.index') }}" class="block bg-white rounded-2xl p-5 border border-black/5 hover:border-black/10 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate">Projetos</p>
                        <span class="text-[10px] font-bold text-slate">Ativos</span>
                    </div>
                    <p class="font-display text-2xl font-bold text-ink">{{ $projetosAtivos }}</p>
                    <p class="text-xs text-slate mt-1">em andamento</p>
                    <div class="mt-4 pt-4 border-t border-black/5 text-xs">
                        @if($proximoProjeto)
                            <p class="text-slate">Próximo prazo</p>
                            <p class="font-bold text-ink mt-0.5 truncate">
                                {{ \Carbon\Carbon::parse($proximoProjeto->data_previsao)->format('d/m') }}
                                <span class="text-slate font-normal">· {{ \Illuminate\Support\Str::limit($proximoProjeto->nome, 22) }}</span>
                            </p>
                        @else
                            <p class="text-slate">Nenhum prazo à vista</p>
                        @endif
                    </div>
                </a>
            </div>
        </section>

        {{-- =================================================================
             Rodapé · Dados da empresa
             ================================================================= --}}
        <section class="bg-white border border-black/5 rounded-3xl p-6 sm:p-7">
            <div class="flex items-center justify-between gap-4 mb-5 flex-wrap">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate">Sua conta na NC5</p>
                    <h3 class="font-display font-bold text-ink text-lg">Dados cadastrais</h3>
                </div>
                <a href="{{ route('customer.support') }}" class="text-xs font-bold text-slate hover:text-bruce transition-colors">
                    Atualizar dados →
                </a>
            </div>
            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
                <div>
                    <dt class="text-[10px] font-bold text-slate uppercase tracking-wider">Razão social</dt>
                    <dd class="mt-1 text-ink font-semibold truncate">{{ $cliente->razao_social }}</dd>
                </div>
                <div>
                    <dt class="text-[10px] font-bold text-slate uppercase tracking-wider">{{ $cliente->tipo_pessoa ?? 'Documento' }}</dt>
                    <dd class="mt-1 text-ink font-semibold font-mono">{{ $cliente->cpf_cnpj ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-[10px] font-bold text-slate uppercase tracking-wider">WhatsApp</dt>
                    <dd class="mt-1 text-ink font-semibold">{{ $cliente->telefone ?? '—' }}</dd>
                </div>
            </dl>
        </section>
    </div>
</x-app-layout>
