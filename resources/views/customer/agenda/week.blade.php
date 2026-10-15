<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Agenda — Semana</h2>
                <p class="text-slate text-sm mt-1">{{ $weekStart->isoFormat('DD/MM') }} a {{ $weekEnd->isoFormat('DD/MM/YYYY') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="inline-flex rounded-xl bg-white border border-black/10 p-1 text-sm">
                    <a href="{{ route('customer.agenda.index') }}" class="px-3 py-1.5 rounded-lg font-bold text-slate hover:text-ink">Lista</a>
                    <a href="{{ route('customer.agenda.index', ['view' => 'week']) }}" class="px-3 py-1.5 rounded-lg font-bold bg-bruce text-white">Semana</a>
                </div>
                <a href="{{ route('customer.agenda.create') }}"
                   class="px-5 py-2.5 bg-bruce hover:bg-bruce/90 text-white rounded-xl font-bold text-sm transition-colors">
                    + Novo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Nav semana --}}
        <div class="flex items-center justify-between gap-3 bg-white border border-black/5 rounded-2xl px-4 py-3 shadow-sm">
            <a href="{{ route('customer.agenda.index', ['view' => 'week', 'week' => $prevWeek]) }}"
               class="text-sm text-slate hover:text-ink">← Semana anterior</a>
            <a href="{{ route('customer.agenda.index', ['view' => 'week', 'week' => $thisWeek]) }}"
               class="text-sm font-bold text-bruce hover:underline">Esta semana</a>
            <a href="{{ route('customer.agenda.index', ['view' => 'week', 'week' => $nextWeek]) }}"
               class="text-sm text-slate hover:text-ink">Próxima semana →</a>
        </div>

        {{-- Grid 7 colunas --}}
        <div class="bg-white border border-black/5 rounded-3xl shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-7 divide-y md:divide-y-0 md:divide-x divide-black/5">
                @foreach($days as $day)
                    <div class="min-h-[300px] {{ $day['isToday'] ? 'bg-bruce/5' : '' }}">
                        <div class="px-3 py-2 border-b border-black/5 bg-slate-50/50">
                            <p class="text-xs font-bold uppercase tracking-wider {{ $day['isToday'] ? 'text-bruce' : 'text-slate' }}">
                                {{ $day['label'] }}
                            </p>
                        </div>
                        <div class="p-2 space-y-2">
                            @forelse($day['appointments'] as $apt)
                                <a href="{{ route('customer.agenda.edit', $apt) }}"
                                   class="block rounded-lg p-2 text-xs border
                                   @if($apt->status === 'cancelado') bg-rose-50 border-rose-100 text-rose-700 line-through opacity-70
                                   @elseif($apt->status === 'concluido') bg-emerald-50 border-emerald-100 text-emerald-700
                                   @elseif($apt->status === 'confirmado') bg-blue-50 border-blue-100 text-blue-800
                                   @else bg-white border-black/10 hover:border-bruce text-ink
                                   @endif">
                                    <p class="font-bold">{{ $apt->scheduled_at->format('H:i') }} · {{ $apt->duration_minutes }}min</p>
                                    <p class="line-clamp-2 mt-0.5">{{ $apt->titulo }}</p>
                                    @if($apt->clienteFinal?->nome_responsavel || $apt->contato_nome)
                                        <p class="text-slate/70 mt-0.5">{{ $apt->clienteFinal?->nome_responsavel ?: $apt->contato_nome }}</p>
                                    @endif
                                    @if($apt->resource)
                                        <p class="text-slate/60 mt-0.5">{{ $apt->resource->nome }}</p>
                                    @endif
                                    @if($apt->created_via === 'bot')
                                        <span class="inline-block mt-1 text-[10px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded-full font-bold">via WhatsApp</span>
                                    @endif
                                </a>
                            @empty
                                <p class="text-xs text-slate/60 text-center py-6">—</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
