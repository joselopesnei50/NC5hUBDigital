<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
            <div>
                <p class="text-xs font-bold text-magenta uppercase tracking-widest mb-2">Gabinete NC5</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Boa {{ now()->hour < 12 ? 'manhã' : (now()->hour < 18 ? 'tarde' : 'noite') }}, {{ Str::before(Auth::user()->name, ' ') }}.</h2>
                <p class="text-slate text-sm mt-1">Visão consolidada da operação · {{ now()->translatedFormat('l, d \d\e F') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.clientes.create') }}" class="bg-ink hover:bg-bruce text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg shadow-ink/10 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Novo cliente
                </a>
            </div>
        </div>
    </x-slot>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white border border-black/5 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Ativos</span>
            </div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate">Clientes</p>
            <h3 class="font-display text-4xl font-bold text-ink mt-1">{{ $clientesAtivos }}</h3>
            <a href="{{ route('admin.clientes.index') }}" class="mt-4 inline-flex text-xs font-bold text-slate hover:text-bruce transition-colors">Ver todos →</a>
        </div>

        <div class="bg-white border border-black/5 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div class="w-11 h-11 bg-ink/5 rounded-xl flex items-center justify-center text-ink">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate">Contratos vigentes</p>
            <h3 class="font-display text-4xl font-bold text-ink mt-1">{{ $contratosVigentes }}</h3>
            <a href="{{ route('admin.contratos.index') }}" class="mt-4 inline-flex text-xs font-bold text-slate hover:text-bruce transition-colors">Gerir contratos →</a>
        </div>

        <div class="bg-ink border border-ink rounded-2xl p-6 shadow-lg shadow-ink/10 text-white relative overflow-hidden">
            <div class="absolute -bottom-16 -right-16 w-40 h-40 bg-magenta/20 rounded-full blur-[60px] pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-11 h-11 bg-white/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-magenta bg-magenta/10 px-2 py-0.5 rounded-full">Mês</span>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-white/50">Faturamento pago</p>
                <h3 class="font-display text-3xl font-bold text-white mt-1">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</h3>
                <p class="mt-3 text-xs text-white/60">{{ $faturasPendentes }} fatura(s) pendente(s)</p>
            </div>
        </div>

        <div class="bg-white border border-black/5 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div class="w-11 h-11 bg-magenta/10 rounded-xl flex items-center justify-center text-magenta">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                </div>
                @if($ticketsAbertos > 0)
                    <span class="text-[10px] font-bold uppercase tracking-wider text-magenta bg-magenta/10 px-2 py-0.5 rounded-full">Atenção</span>
                @endif
            </div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate">Tickets abertos</p>
            <h3 class="font-display text-4xl font-bold text-ink mt-1">{{ $ticketsAbertos }}</h3>
            <a href="{{ route('admin.tickets.index') }}" class="mt-4 inline-flex text-xs font-bold text-slate hover:text-bruce transition-colors">Ver fila →</a>
        </div>
    </div>

    <!-- Últimos clientes + leads -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white border border-black/5 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-black/5 flex justify-between items-center">
                <div>
                    <h3 class="font-display text-lg font-bold text-ink">Últimos clientes integrados</h3>
                    <p class="text-xs text-slate mt-0.5">Os 5 mais recentes</p>
                </div>
                <a href="{{ route('admin.clientes.index') }}" class="text-xs font-bold text-slate hover:text-bruce transition-colors uppercase tracking-wider">Ver todos</a>
            </div>
            @if($ultimosClientes->isEmpty())
                <div class="p-10 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <p class="text-slate">Nenhum cliente cadastrado ainda.</p>
                    <a href="{{ route('admin.clientes.create') }}" class="mt-4 inline-flex text-sm font-bold text-magenta hover:text-ink">Criar o primeiro →</a>
                </div>
            @else
                <ul class="divide-y divide-black/5">
                    @foreach($ultimosClientes as $c)
                        <li class="p-5 hover:bg-mist transition-colors flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-ink text-white flex items-center justify-center font-display font-bold flex-shrink-0">{{ substr($c->razao_social, 0, 1) }}</div>
                                <div class="min-w-0">
                                    <p class="font-bold text-ink text-sm truncate">{{ $c->razao_social }}</p>
                                    <p class="text-xs text-slate truncate">{{ $c->user->email ?? '—' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.clientes.show', $c->id) }}" class="text-xs font-bold text-slate hover:text-bruce transition-colors uppercase tracking-wider flex-shrink-0">Abrir</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white border border-black/5 rounded-2xl shadow-sm">
            <div class="px-6 py-5 border-b border-black/5">
                <h3 class="font-display text-lg font-bold text-ink">Últimos leads (IA)</h3>
                <p class="text-xs text-slate mt-0.5">Diagnóstico gratuito</p>
            </div>
            @if($ultimosLeads->isEmpty())
                <div class="p-8 text-center text-sm text-slate">Nenhum lead capturado ainda.</div>
            @else
                <ul class="divide-y divide-black/5">
                    @foreach($ultimosLeads as $lead)
                        <li class="p-5 hover:bg-mist transition-colors">
                            <p class="font-bold text-ink text-sm truncate">{{ $lead->nome }}</p>
                            <p class="text-xs text-slate truncate">{{ $lead->email }}</p>
                            <div class="mt-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider">
                                <span class="px-2 py-0.5 bg-mist text-slate rounded-full">{{ str_replace('_', ' ', $lead->tipo_analise) }}</span>
                                <span class="text-slate">{{ $lead->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-admin-layout>
