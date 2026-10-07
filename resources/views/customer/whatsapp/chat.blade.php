<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('customer.whatsapp.inbox') }}" class="text-slate hover:text-ink">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center">
                {{ strtoupper(substr($chat->contact_name ?: $chat->contact_phone ?: 'C', 0, 1)) }}
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-ink leading-tight">
                    {{ $chat->clienteFinal?->nome_responsavel ?: ($chat->contact_name ?: $chat->contact_phone) }}
                </h2>
                <p class="text-xs text-slate">{{ $chat->contact_phone ?: $chat->wa_id }}</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white border border-black/5 rounded-3xl shadow-sm flex flex-col" style="height: calc(100vh - 220px);">
        @if(session('error'))
            <div class="bg-rose-50 border-b border-rose-200 text-rose-800 px-5 py-3 text-sm">{{ session('error') }}</div>
        @endif

        <div id="messages-area" class="flex-1 overflow-y-auto p-6 space-y-2 bg-mist/40">
            @foreach($messages as $msg)
                <div class="flex {{ $msg->isInbound() ? 'justify-start' : 'justify-end' }}">
                    <div class="max-w-md rounded-2xl px-4 py-2.5 shadow-sm
                        {{ $msg->isInbound() ? 'bg-white text-ink' : 'bg-emerald-600 text-white' }}">
                        @if($msg->type !== 'chat')
                            <p class="text-xs opacity-75 italic mb-1">[{{ $msg->type }}]</p>
                        @endif
                        <p class="text-sm whitespace-pre-wrap break-words">{{ $msg->content ?: $msg->media_caption }}</p>
                        <p class="text-[10px] opacity-70 mt-1 text-right">
                            {{ $msg->created_at->format('H:i') }}
                            @if(!$msg->isInbound())
                                · {{ $msg->status === 'failed' ? '❌' : '✓' }}
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('customer.whatsapp.send', $chat) }}" class="border-t border-black/5 p-4 flex gap-3">
            @csrf
            <input type="text" name="content" required maxlength="4000"
                   placeholder="Digite uma mensagem..."
                   autocomplete="off"
                   class="flex-1 rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" />
            <button type="submit"
                    @if(!$instance?->isConnected()) disabled @endif
                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Enviar
            </button>
        </form>
    </div>

    <script>
        (function() {
            const area = document.getElementById('messages-area');
            if (area) area.scrollTop = area.scrollHeight;
        })();
    </script>
</x-app-layout>
