<nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 md:relative md:translate-x-0 bg-white border-r border-black/5 w-64 h-full flex flex-col flex-shrink-0 shadow-xl md:shadow-none transition-transform duration-300">

    <div class="overflow-y-auto no-scrollbar flex-1">
        <div class="h-20 flex items-center px-6 border-b border-black/5">
            <a href="{{ route('customer.index') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.svg') }}" alt="NC5 Logo" class="h-7 w-auto">
            </a>
        </div>

        <div class="px-4 py-6 space-y-1">
            @php $items = [
                ['route'=>'customer.index',      'label'=>'Visão Geral',     'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                ['route'=>'customer.contracts',  'label'=>'Meus Contratos',  'icon'=>'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                ['route'=>'customer.invoices',   'label'=>'Faturas',         'icon'=>'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
                ['route'=>'customer.materiais',  'label'=>'Aprovar Materiais','icon'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route'=>'customer.briefings',  'label'=>'Meus Briefings',  'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ]; @endphp

            @foreach($items as $item)
                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs($item['route']) ? 'bg-ink text-white font-semibold shadow-md shadow-ink/10' : 'text-slate hover:text-ink hover:bg-mist' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach

            <div class="pt-6 mt-2">
                <p class="px-3 text-[10px] font-bold text-slate uppercase tracking-widest mb-3">Ajuda</p>
                <a href="{{ route('customer.support') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('customer.support*') ? 'bg-ink text-white font-semibold shadow-md shadow-ink/10' : 'text-slate hover:text-ink hover:bg-mist' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    Suporte
                </a>
            </div>
        </div>
    </div>

    <div class="p-4 border-t border-black/5 flex-shrink-0">
        <div class="flex items-center gap-3 px-2 py-2 mb-1">
            <div class="w-10 h-10 rounded-xl bg-ink flex items-center justify-center text-sm font-bold text-white">{{ substr(Auth::user()->name ?? 'C', 0, 1) }}</div>
            <div class="overflow-hidden">
                <p class="text-sm font-semibold text-ink truncate">{{ Auth::user()->name ?? 'Cliente' }}</p>
                <p class="text-[10px] text-slate font-bold uppercase tracking-wider">Área Restrita</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-3 py-2 text-sm text-magenta hover:bg-bruce/5 rounded-xl transition-colors flex items-center gap-2 font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Sair
            </button>
        </form>
    </div>
</nav>
