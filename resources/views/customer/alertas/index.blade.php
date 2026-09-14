<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Alertas do Bruce</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">O que precisa da sua atenção</h2>
            <p class="text-slate text-sm mt-1">O Bruce analisa sua operação todo dia e cria alertas quando encontra algo que merece uma decisão.</p>
        </div>
    </x-slot>

    @php
        $sevMap = [
            'critico' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'dot' => 'bg-rose-500', 'label' => 'Crítico', 'labelBg' => 'bg-rose-100 text-rose-700'],
            'atencao' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Atenção', 'labelBg' => 'bg-amber-100 text-amber-800'],
            'info'    => ['bg' => 'bg-sky-50',   'border' => 'border-sky-200',   'dot' => 'bg-sky-500',  'label' => 'Info',    'labelBg' => 'bg-sky-100 text-sky-800'],
        ];
    @endphp

    <div class="space-y-6">
        <section>
            <h3 class="font-display font-bold text-ink text-lg mb-3">Ativos ({{ $ativos->count() }})</h3>

            @if($ativos->isEmpty())
                <div class="bg-white border border-black/5 rounded-3xl p-8 shadow-sm text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="font-display font-bold text-ink">Nada urgente por aqui.</p>
                    <p class="text-slate text-sm mt-1">A próxima análise do Bruce roda amanhã às 08:30.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($ativos as $a)
                        @php $s = $sevMap[$a->severidade] ?? $sevMap['info']; @endphp
                        <div class="{{ $s['bg'] }} border {{ $s['border'] }} rounded-2xl p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-4 flex-wrap">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-2">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider {{ $s['labelBg'] }} px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                                            {{ $s['label'] }}
                                        </span>
                                        @if(!$a->lido_em)
                                            <span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider bg-ink text-white px-2 py-0.5 rounded-full">Novo</span>
                                        @endif
                                        <span class="text-xs text-slate">{{ $a->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="font-display font-bold text-ink text-base">{{ $a->titulo }}</h4>
                                    <p class="text-slate text-sm mt-1 leading-relaxed">{{ $a->mensagem }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(!$a->lido_em)
                                        <form action="{{ route('customer.alertas.lido', $a) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-slate hover:text-ink px-3 py-1.5 rounded-full border border-slate/20 hover:border-ink transition-colors">
                                                Marcar como lido
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('customer.alertas.dispensar', $a) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-white bg-ink hover:bg-bruce px-3 py-1.5 rounded-full transition-colors">
                                            Dispensar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        @if($arquivados->isNotEmpty())
            <section>
                <h3 class="font-display font-bold text-ink text-lg mb-3">Arquivados</h3>
                <div class="bg-white border border-black/5 rounded-3xl divide-y divide-black/5 shadow-sm overflow-hidden">
                    @foreach($arquivados as $a)
                        @php $s = $sevMap[$a->severidade] ?? $sevMap['info']; @endphp
                        <div class="p-4 flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider {{ $s['labelBg'] }} px-1.5 py-0.5 rounded-full">
                                        {{ $s['label'] }}
                                    </span>
                                    <span class="text-xs text-slate">Dispensado {{ $a->dispensado_em?->diffForHumans() }}</span>
                                </div>
                                <p class="text-ink text-sm mt-1 truncate">{{ $a->titulo }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
