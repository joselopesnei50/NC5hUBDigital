<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-magenta uppercase tracking-widest mb-2">Portal do cliente</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Bem-vindo(a), {{ Str::before(Auth::user()->name, ' ') }}.</h2>
            <p class="text-slate text-sm mt-1">Seu ecossistema com a NC5 Hub · {{ now()->translatedFormat('l, d \d\e F') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Banner -->
        <div class="relative bg-ink rounded-3xl p-8 lg:p-10 text-white overflow-hidden shadow-xl shadow-ink/10">
            <div class="absolute -top-32 -right-24 w-80 h-80 bg-magenta/20 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <p class="text-xs font-bold text-magenta uppercase tracking-widest mb-3">Sua conta</p>
                    <h1 class="font-display font-bold text-3xl md:text-4xl leading-tight">{{ $cliente->razao_social }}</h1>
                    <p class="text-white/60 mt-3 text-sm max-w-lg">Aqui você acompanha contratos, faturas, materiais em produção e briefings — tudo num só lugar.</p>
                </div>
                <a href="{{ route('customer.support') }}" class="bg-magenta hover:bg-white hover:text-ink text-white px-6 py-3 rounded-full font-bold text-sm transition-all shadow-lg whitespace-nowrap">
                    Precisa de ajuda?
                </a>
            </div>
        </div>

        @if($contratosPendentes > 0)
        <a href="{{ route('customer.contracts') }}" class="group block bg-magenta/5 border border-magenta/20 hover:border-bruce/40 rounded-2xl p-5 transition-colors">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-magenta rounded-xl flex items-center justify-center text-white shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-ink text-sm">Ação necessária: assinatura pendente</p>
                        <p class="text-slate text-xs">Você tem {{ $contratosPendentes }} contrato(s) aguardando sua assinatura.</p>
                    </div>
                </div>
                <span class="text-magenta font-bold text-sm group-hover:translate-x-1 transition-transform hidden sm:block">Assinar agora →</span>
            </div>
        </a>
        @endif

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm">
                <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate">Status da conta</p>
                <p class="font-display text-3xl font-bold text-ink mt-1">Ativa</p>
                <p class="text-xs text-emerald-600 mt-2 font-bold uppercase tracking-wider">Operando</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm">
                <div class="w-11 h-11 {{ $faturasPendentes > 0 ? 'bg-orange-50 text-orange-600' : 'bg-emerald-50 text-emerald-600' }} rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate">Faturas pendentes</p>
                <p class="font-display text-3xl font-bold text-ink mt-1">{{ $faturasPendentes }}</p>
                <p class="text-xs mt-2 font-bold uppercase tracking-wider {{ $faturasPendentes > 0 ? 'text-orange-600' : 'text-emerald-600' }}">
                    {{ $faturasPendentes > 0 ? 'Atenção ao vencimento' : 'Tudo em dia' }}
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-black/5 shadow-sm">
                <div class="w-11 h-11 {{ $materiaisAguardando > 0 ? 'bg-magenta/10 text-magenta' : 'bg-emerald-50 text-emerald-600' }} rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate">Materiais p/ aprovar</p>
                <p class="font-display text-3xl font-bold text-ink mt-1">{{ $materiaisAguardando }}</p>
                <p class="text-xs mt-2 font-bold uppercase tracking-wider {{ $materiaisAguardando > 0 ? 'text-magenta' : 'text-emerald-600' }}">
                    {{ $materiaisAguardando > 0 ? 'Aguardando você' : 'Nada pendente' }}
                </p>
            </div>
        </div>

        <!-- Ações + dados -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 bg-white border border-black/5 rounded-2xl p-6 shadow-sm">
                <h3 class="font-display text-lg font-bold text-ink mb-5">Ações rápidas</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <a href="{{ route('customer.materiais') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-xs font-semibold uppercase tracking-wider">Materiais</span>
                        <span class="font-display text-xl font-bold mt-1">Aprovar</span>
                    </a>
                    <a href="{{ route('customer.invoices') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-xs font-semibold uppercase tracking-wider">Faturas</span>
                        <span class="font-display text-xl font-bold mt-1">Pagar</span>
                    </a>
                    <a href="{{ route('customer.briefings') }}" class="group flex flex-col p-5 bg-mist hover:bg-ink hover:text-white rounded-2xl transition-colors">
                        <span class="text-slate group-hover:text-white/60 text-xs font-semibold uppercase tracking-wider">Briefings</span>
                        <span class="font-display text-xl font-bold mt-1">Responder</span>
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
