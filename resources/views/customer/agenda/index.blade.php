<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Agenda</h2>
                <p class="text-slate text-sm mt-1">Seus agendamentos, criados manualmente ou pelo bot do WhatsApp.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="inline-flex rounded-xl bg-white border border-black/10 p-1 text-sm">
                    <a href="{{ route('customer.agenda.index') }}" class="px-3 py-1.5 rounded-lg font-bold bg-bruce text-white">Lista</a>
                    <a href="{{ route('customer.agenda.index', ['view' => 'week']) }}" class="px-3 py-1.5 rounded-lg font-bold text-slate hover:text-ink">Semana</a>
                </div>
                <a href="{{ route('customer.agenda.create') }}"
                   class="px-5 py-2.5 bg-bruce hover:bg-bruce/90 text-white rounded-xl font-bold text-sm transition-colors">
                    + Novo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl text-sm">{{ session('success') }}</div>
        @endif

        {{-- Link ICS (sincronizar com Google Calendar / Outlook / Apple) --}}
        <details class="bg-mist/40 border border-black/5 rounded-2xl group" x-data="{ copied: false }">
            <summary class="cursor-pointer px-5 py-3 text-sm font-bold text-ink flex items-center gap-2">
                <svg class="w-5 h-5 text-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Sincronizar com Google Calendar / Outlook / Apple
                <span class="text-xs text-slate font-normal ml-auto">clique pra expandir</span>
            </summary>
            <div class="px-5 pb-4 space-y-3 text-sm">
                <p class="text-slate">Importe este link no seu app de calendário favorito. Os agendamentos aparecem automaticamente e são atualizados sozinhos.</p>
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="text" readonly value="{{ $icsUrl }}" x-ref="icsInput"
                           class="flex-1 min-w-[280px] px-3 py-2 bg-white border border-black/10 rounded-lg text-xs font-mono text-slate">
                    <button type="button"
                            @click="navigator.clipboard.writeText($refs.icsInput.value); copied = true; setTimeout(() => copied = false, 2000)"
                            class="px-4 py-2 bg-bruce hover:bg-bruce/90 text-white rounded-lg text-xs font-bold">
                        <span x-show="!copied">Copiar</span>
                        <span x-show="copied" x-cloak>Copiado!</span>
                    </button>
                    <form method="POST" action="{{ route('customer.calendar.cliente.regenerate') }}" onsubmit="return confirm('O link antigo deixará de funcionar. Confirma?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white border border-black/10 hover:border-rose-400 text-rose-700 rounded-lg text-xs font-bold">Regenerar</button>
                    </form>
                </div>
                <p class="text-slate text-xs">
                    <strong>Google Calendar:</strong> Configurações → Adicionar calendário → De URL → cole o link.
                    <strong>Outlook/Apple:</strong> Assine o calendário usando este endereço.
                </p>
            </div>
        </details>

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
                                    @if($apt->resource) · <span class="text-ink font-bold">{{ $apt->resource->nome }}</span>@endif
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
