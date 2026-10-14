<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Agenda</h2>
                <p class="text-slate text-sm mt-1">Seus agendamentos, criados manualmente ou pelo bot do WhatsApp.</p>
            </div>
            <a href="{{ route('customer.agenda.create') }}"
               class="px-5 py-2.5 bg-bruce hover:bg-bruce/90 text-white rounded-xl font-bold text-sm transition-colors">
                + Novo agendamento
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl text-sm">{{ session('success') }}</div>
        @endif

        {{-- Proximos --}}
        <div>
            <h3 class="font-display font-bold text-lg text-ink mb-3">Próximos</h3>
            @if($upcoming->isEmpty())
                <div class="bg-mist/60 border border-black/5 rounded-2xl p-8 text-center text-slate text-sm">
                    Nenhum agendamento futuro. <a href="{{ route('customer.agenda.create') }}" class="text-bruce font-bold hover:underline">Criar agora</a>.
                </div>
            @else
                <div class="bg-white border border-black/5 rounded-3xl shadow-sm divide-y divide-black/5">
                    @foreach($upcoming as $apt)
                        <div class="p-5 flex items-center gap-4 flex-wrap">
                            <div class="w-16 text-center">
                                <p class="text-xs font-bold text-slate uppercase">{{ $apt->scheduled_at->isoFormat('MMM') }}</p>
                                <p class="font-display font-bold text-2xl text-ink leading-tight">{{ $apt->scheduled_at->format('d') }}</p>
                                <p class="text-xs text-slate">{{ $apt->scheduled_at->format('H:i') }}</p>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-bold text-ink">{{ $apt->titulo }}</p>
                                    @if($apt->created_via === 'bot')
                                        <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">via WhatsApp</span>
                                    @endif
                                    @if($apt->status === 'confirmado')
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-bold">confirmado</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate mt-1">
                                    {{ $apt->clienteFinal?->nome_responsavel ?: $apt->contato_nome ?: '—' }}
                                    @if($apt->contato_telefone) · {{ $apt->contato_telefone }}@endif
                                    · {{ $apt->duration_minutes }}min
                                </p>
                                @if($apt->descricao)
                                    <p class="text-xs text-slate mt-1 line-clamp-2">{{ $apt->descricao }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('customer.agenda.edit', $apt) }}" class="text-slate hover:text-ink text-sm">Editar</a>
                                <form method="POST" action="{{ route('customer.agenda.complete', $apt) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-700 hover:text-emerald-900 text-sm font-bold">Concluir</button>
                                </form>
                                <form method="POST" action="{{ route('customer.agenda.cancel', $apt) }}" class="inline" onsubmit="return confirm('Cancelar este agendamento?')">
                                    @csrf
                                    <button type="submit" class="text-rose-700 hover:text-rose-900 text-sm">Cancelar</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Historico --}}
        <div>
            <h3 class="font-display font-bold text-lg text-ink mb-3">Histórico</h3>
            @if($history->isEmpty())
                <p class="text-slate text-sm">Sem agendamentos passados.</p>
            @else
                <div class="bg-white border border-black/5 rounded-3xl shadow-sm divide-y divide-black/5">
                    @foreach($history as $apt)
                        <div class="p-5 flex items-center gap-4 flex-wrap">
                            <div class="w-16 text-center opacity-60">
                                <p class="text-xs font-bold text-slate uppercase">{{ $apt->scheduled_at->isoFormat('MMM') }}</p>
                                <p class="font-display font-bold text-xl text-ink leading-tight">{{ $apt->scheduled_at->format('d') }}</p>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-bold text-ink">{{ $apt->titulo }}</p>
                                    @if($apt->status === 'concluido')
                                        <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full">concluído</span>
                                    @elseif($apt->status === 'cancelado')
                                        <span class="text-xs bg-rose-100 text-rose-800 px-2 py-0.5 rounded-full">cancelado</span>
                                    @else
                                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">não realizado</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate mt-1">
                                    {{ $apt->scheduled_at->format('d/m/Y H:i') }} · {{ $apt->clienteFinal?->nome_responsavel ?: $apt->contato_nome ?: '—' }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('customer.agenda.destroy', $apt) }}" onsubmit="return confirm('Excluir permanentemente?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate hover:text-rose-700 text-xs">Excluir</button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $history->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
