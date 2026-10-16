<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.hub.whatsapp.inbox') }}" class="text-slate hover:text-ink">
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
            @if(!$chat->is_group)
                <form method="POST" action="{{ route('admin.hub.whatsapp.bot.toggle', $chat) }}">
                    @csrf
                    <button type="submit"
                            class="px-3 py-2 rounded-xl text-xs font-bold transition-colors {{ $chat->is_bot_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $chat->is_bot_active ? 'Bot ativo — pausar' : 'Bot pausado — ativar' }}
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="bg-white border border-black/5 rounded-3xl shadow-sm flex flex-col" style="height: calc(100vh - 220px);">
        @if(session('error'))
            <div class="bg-rose-50 border-b border-rose-200 text-rose-800 px-5 py-3 text-sm">{{ session('error') }}</div>
        @endif

        <div id="messages-area" class="flex-1 overflow-y-auto p-6 space-y-2 bg-mist/40" data-latest-id="{{ $messages->last()?->id ?? 0 }}">
            @foreach($messages as $msg)
                <div class="flex {{ $msg->isInbound() ? 'justify-start' : 'justify-end' }}" data-msg-id="{{ $msg->id }}">
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

        <form method="POST" action="{{ route('admin.hub.whatsapp.send', $chat) }}" class="border-t border-black/5 p-4 flex gap-3">
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
            if (!area) return;
            area.scrollTop = area.scrollHeight;

            const feedUrl = '{{ route('admin.hub.whatsapp.chat.feed', $chat) }}';

            function escapeHtml(s) {
                return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
            }

            function appendMessage(m) {
                const inbound = m.direction === 'inbound';
                const div = document.createElement('div');
                div.className = 'flex ' + (inbound ? 'justify-start' : 'justify-end');
                div.dataset.msgId = m.id;
                const bubble = inbound ? 'bg-white text-ink' : 'bg-emerald-600 text-white';
                const typeLabel = m.type !== 'chat' ? `<p class="text-xs opacity-75 italic mb-1">[${escapeHtml(m.type)}]</p>` : '';
                const statusIcon = inbound ? '' : ' · ' + (m.status === 'failed' ? '❌' : '✓');
                div.innerHTML = `
                    <div class="max-w-md rounded-2xl px-4 py-2.5 shadow-sm ${bubble}">
                        ${typeLabel}
                        <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(m.content || m.caption || '')}</p>
                        <p class="text-[10px] opacity-70 mt-1 text-right">${m.time}${statusIcon}</p>
                    </div>`;
                area.appendChild(div);
            }

            async function poll() {
                try {
                    const after = parseInt(area.dataset.latestId || '0', 10);
                    const res = await fetch(`${feedUrl}?after_id=${after}`, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                    if (!res.ok) return;
                    const data = await res.json();
                    if (!data.messages.length) return;
                    const wasAtBottom = area.scrollHeight - area.scrollTop - area.clientHeight < 100;
                    data.messages.forEach(appendMessage);
                    area.dataset.latestId = data.latest_id;
                    if (wasAtBottom) area.scrollTop = area.scrollHeight;
                } catch (e) {}
            }

            setInterval(poll, 3000);
        })();
    </script>
</x-admin-layout>
