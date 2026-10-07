<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">WhatsApp — Inbox</h2>
            <p class="text-slate text-sm mt-1">Mensagens recebidas no número conectado. Clique numa conversa pra abrir.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(!$instance || !$instance->isConnected())
            <div class="bg-amber-50 border border-amber-200 text-amber-900 px-5 py-4 rounded-xl text-sm">
                WhatsApp não está conectado.
                <a href="{{ route('customer.whatsapp.conexao') }}" class="font-bold underline">Conectar agora</a>.
            </div>
        @endif

        <div class="bg-white border border-black/5 rounded-3xl shadow-sm overflow-hidden">
            @if($chats->isEmpty())
                <div class="text-center py-16 text-slate text-sm">
                    Nenhuma conversa ainda. Quando alguém mandar mensagem pro seu número, aparece aqui.
                </div>
            @else
                <ul class="divide-y divide-black/5">
                    @foreach($chats as $chat)
                        <li>
                            <a href="{{ route('customer.whatsapp.chat', $chat) }}"
                               class="flex items-center gap-4 px-5 py-4 hover:bg-mist transition-colors">
                                <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center">
                                    {{ strtoupper(substr($chat->contact_name ?: $chat->contact_phone ?: 'C', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline gap-3">
                                        <p class="font-bold text-ink truncate">
                                            {{ $chat->clienteFinal?->nome_responsavel ?: ($chat->contact_name ?: $chat->contact_phone) }}
                                        </p>
                                        <span class="text-xs text-slate shrink-0">
                                            {{ $chat->last_message_at?->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate truncate mt-0.5">
                                        {{ $chat->latestMessage?->content ?: '—' }}
                                    </p>
                                </div>
                                @if($chat->unread_count > 0)
                                    <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-2 bg-emerald-600 text-white rounded-full text-xs font-bold">
                                        {{ $chat->unread_count }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="px-5 py-4 border-t border-black/5">
                    {{ $chats->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
