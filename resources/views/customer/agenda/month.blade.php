<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Agenda — {{ $monthStart->isoFormat('MMMM YYYY') }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <div class="inline-flex rounded-xl bg-white border border-black/10 p-1 text-sm">
                    <a href="{{ route('customer.agenda.index') }}" class="px-3 py-1.5 rounded-lg font-bold text-slate hover:text-ink">Lista</a>
                    <a href="{{ route('customer.agenda.index', ['view' => 'week']) }}" class="px-3 py-1.5 rounded-lg font-bold text-slate hover:text-ink">Semana</a>
                    <a href="{{ route('customer.agenda.index', ['view' => 'month']) }}" class="px-3 py-1.5 rounded-lg font-bold bg-bruce text-white">Mês</a>
                </div>
                <a href="{{ route('customer.agenda.create') }}"
                   class="px-5 py-2.5 bg-bruce hover:bg-bruce/90 text-white rounded-xl font-bold text-sm transition-colors">
                    + Novo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Nav mes --}}
        <div class="flex items-center justify-between gap-3 bg-white border border-black/5 rounded-2xl px-4 py-3 shadow-sm">
            <a href="{{ route('customer.agenda.index', ['view' => 'month', 'month' => $prevMonth]) }}"
               class="text-sm text-slate hover:text-ink">← Mês anterior</a>
            <a href="{{ route('customer.agenda.index', ['view' => 'month', 'month' => $thisMonth]) }}"
               class="text-sm font-bold text-bruce hover:underline">Este mês</a>
            <a href="{{ route('customer.agenda.index', ['view' => 'month', 'month' => $nextMonth]) }}"
               class="text-sm text-slate hover:text-ink">Próximo mês →</a>
        </div>

        {{-- Grid mes --}}
        <div class="bg-white border border-black/5 rounded-3xl shadow-sm overflow-hidden">
            {{-- Cabeçalho dos dias --}}
            <div class="grid grid-cols-7 bg-slate-50/80 border-b border-black/5">
                @foreach(['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'] as $label)
                    <div class="px-3 py-2 text-center text-xs font-bold text-slate uppercase tracking-wider">{{ $label }}</div>
                @endforeach
            </div>

            {{-- Semanas --}}
            @foreach($weeks as $week)
                <div class="grid grid-cols-7 divide-x divide-black/5 border-b border-black/5 last:border-b-0">
                    @foreach($week as $day)
                        <a href="{{ route('customer.agenda.index', ['view' => 'week', 'week' => $day['key']]) }}"
                           class="min-h-[110px] p-2 flex flex-col gap-1 transition-colors
                           {{ $day['inMonth'] ? '' : 'bg-slate-50/40 opacity-50' }}
                           {{ $day['isToday'] ? 'bg-bruce/5' : '' }}
                           hover:bg-bruce/5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold {{ $day['isToday'] ? 'bg-bruce text-white rounded-full w-6 h-6 flex items-center justify-center' : 'text-ink' }}">
                                    {{ $day['date']->format('d') }}
                                </span>
                                @if($day['total'] > 0)
                                    <span class="text-[10px] text-slate font-bold">{{ $day['total'] }}</span>
                                @endif
                            </div>
                            @foreach($day['appointments'] as $apt)
                                <div class="text-[11px] px-1.5 py-0.5 rounded truncate
                                @if($apt->status === 'cancelado') bg-rose-50 text-rose-700 line-through
                                @elseif($apt->status === 'concluido') bg-emerald-50 text-emerald-700
                                @elseif($apt->status === 'confirmado') bg-blue-50 text-blue-800
                                @else bg-slate-100 text-slate-700
                                @endif">
                                    <strong>{{ $apt->scheduled_at->format('H:i') }}</strong> {{ $apt->titulo }}
                                </div>
                            @endforeach
                            @if($day['overflow'] > 0)
                                <div class="text-[10px] text-slate font-bold">+{{ $day['overflow'] }} mais</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>

        <p class="text-xs text-slate text-center">Clique em qualquer dia para ver a semana em detalhes.</p>
    </div>
</x-app-layout>
