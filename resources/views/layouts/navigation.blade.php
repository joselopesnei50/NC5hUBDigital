<nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 md:relative md:translate-x-0 bg-ink text-white w-64 h-full flex flex-col flex-shrink-0 shadow-2xl md:shadow-none transition-transform duration-300">

    <div class="overflow-y-auto no-scrollbar flex-1 px-4 py-6">
        <!-- Logo & Client Brand -->
        <div class="h-16 flex items-center px-3 mb-6 border-b border-white/10">
            <a href="{{ route('customer.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-claro.svg') }}" alt="NC5 Hub Digital" class="h-8 w-auto">
                <span class="text-[9px] font-extrabold text-bruce uppercase tracking-widest bg-bruce/10 px-2 py-0.5 rounded-md border border-bruce/20">Cliente</span>
            </a>
        </div>

        {{--
            Menu em 3 grupos colapsaveis + item solto no topo.
            Cada grupo abre automaticamente quando alguma das rotas dentro esta ativa
            (via atributo `open` avaliado no server-side com routeIs()).
            Groups usam <details>/<summary> HTML5 — zero JS, funciona sem Alpine.
        --}}
        @php
            $groupNC5 = [
                ['route' => 'customer.contracts',  'label' => 'Meus Contratos',    'match' => 'customer.contracts*',  'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 017-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                ['route' => 'customer.invoices',   'label' => 'Minhas Faturas',    'match' => 'customer.invoices*',   'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
                ['route' => 'customer.materiais',  'label' => 'Aprovar Materiais', 'match' => 'customer.materiais*',  'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route' => 'customer.briefings',  'label' => 'Meus Briefings',    'match' => 'customer.briefings*',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['route' => 'customer.support',    'label' => 'Suporte & Tickets', 'match' => 'customer.support*',    'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
            ];
            $groupNegocio = [
                ['route' => 'customer.dashboard-vendas',      'label' => 'Dashboard de Vendas', 'match' => 'customer.dashboard-vendas*', 'icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055zM20.488 9H15V3.512A9.025 9.025 0 0120.488 9z'],
                ['route' => 'customer.clientes-finais.index', 'label' => 'Clientes (CRM)',      'match' => 'customer.clientes-finais*',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                ['route' => 'customer.fornecedores.index',    'label' => 'Fornecedores',        'match' => 'customer.fornecedores*',     'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                ['route' => 'customer.produtos.index',        'label' => 'Produtos e Serviços','match' => 'customer.produtos*',         'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                ['route' => 'customer.pedidos.index',         'label' => 'Gestão de Pedidos',   'match' => 'customer.pedidos*',          'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                ['route' => 'customer.lancamentos.index',     'label' => 'Gestão Financeira',   'match' => 'customer.lancamentos*',      'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['route' => 'customer.projetos.index',        'label' => 'Meus Projetos',       'match' => 'customer.projetos*',         'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                ['route' => 'customer.documentos.index',      'label' => 'Cofre de Documentos','match' => 'customer.documentos*',       'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                ['route' => 'customer.google-business.index', 'label' => 'Google Meu Negócio', 'match' => 'customer.google-business*',  'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ];
            $groupBruce = [
                ['route' => 'customer.alertas.index',        'label' => 'Alertas do Bruce', 'match' => 'customer.alertas*',        'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                ['route' => 'customer.agent-drafts.index',   'label' => 'Mensagens IA',     'match' => 'customer.agent-drafts*',   'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['route' => 'customer.conhecimento.index',   'label' => 'Base do Bruce',    'match' => 'customer.conhecimento*',   'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ];

            $groupIsActive = fn ($items) => collect($items)->contains(fn ($i) => request()->routeIs($i['match']));
        @endphp

        <div class="space-y-1">
            {{-- Visao Geral (solto no topo, sem grupo) --}}
            @php $overviewActive = request()->routeIs('customer.index'); @endphp
            <a href="{{ route('customer.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $overviewActive ? 'bg-bruce text-white shadow-md font-bold' : 'text-white/85 hover:text-white hover:bg-white/10' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Visão Geral
            </a>

            {{-- Grupo 1: Minha Conta NC5 (relacao com a agencia) --}}
            <details class="group pt-4" @if($groupIsActive($groupNC5)) open @endif>
                <summary class="cursor-pointer list-none px-3 pb-2 flex items-center justify-between text-[10px] font-bold text-white/70 uppercase tracking-widest hover:text-white transition-colors">
                    <span>Minha Conta NC5</span>
                    <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="space-y-1">
                    @foreach($groupNC5 as $item)
                        @php $isActive = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-bruce text-white shadow-md font-bold' : 'text-white/85 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </details>

            {{-- Grupo 2: Meu Negocio (uso das ferramentas do cliente) --}}
            <details class="group pt-4" @if($groupIsActive($groupNegocio)) open @endif>
                <summary class="cursor-pointer list-none px-3 pb-2 flex items-center justify-between text-[10px] font-bold text-white/70 uppercase tracking-widest hover:text-white transition-colors">
                    <span>Meu Negócio</span>
                    <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="space-y-1">
                    @foreach($groupNegocio as $item)
                        @php $isActive = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-bruce text-white shadow-md font-bold' : 'text-white/85 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </details>

            {{-- Grupo 3: Bruce IA (assistente) --}}
            <details class="group pt-4" @if($groupIsActive($groupBruce)) open @endif>
                <summary class="cursor-pointer list-none px-3 pb-2 flex items-center justify-between text-[10px] font-bold text-white/70 uppercase tracking-widest hover:text-white transition-colors">
                    <span>Bruce IA</span>
                    <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="space-y-1">
                    @foreach($groupBruce as $item)
                        @php $isActive = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-bruce text-white shadow-md font-bold' : 'text-white/85 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </details>
        </div>
    </div>

    <!-- Client User Card Footer -->
    <div class="p-4 border-t border-white/10 flex-shrink-0 bg-inkLight/50">
        <div class="flex items-center gap-3 px-2 py-2 mb-2">
            <div class="w-10 h-10 rounded-xl bg-bruce flex items-center justify-center text-sm font-bold text-white shadow-md">
                {{ substr(Auth::user()->name ?? 'C', 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Cliente' }}</p>
                <p class="text-[10px] text-bruce font-bold uppercase tracking-wider">Conta Corporativa</p>
            </div>
        </div>
        <a href="{{ route('customer.senha') }}" class="w-full text-left px-3 py-2 text-xs text-white/85 hover:text-white hover:bg-white/10 rounded-xl transition-all flex items-center gap-2 font-semibold mb-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Trocar Senha
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-3 py-2 text-xs text-white/85 hover:text-white hover:bg-rose-500/20 rounded-xl transition-all flex items-center gap-2 font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sair
            </button>
        </form>
    </div>
</nav>
