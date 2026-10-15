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
            <div id="inbox-empty" class="text-center py-16 text-slate text-sm {{ $chats->isEmpty() ? '' : 'hidden' }}">
                Nenhuma conversa ainda. Quando alguém mandar mensagem pro seu número, aparece aqui.
            </div>
            <ul id="inbox-list" class="divide-y divide-black/5 {{ $chats->isEmpty() ? 'hidden' : '' }}">
                @foreach($chats as $chat)
                    <li data-chat-id="{{ $chat->id }}">
                        <a href="{{ route('customer.whatsapp.chat', $chat) }}"
                           class="flex items-center gap-4 px-5 py-4 hover:bg-mist transition-colors">
                            <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center">
                                {{ strtoupper(substr($chat->contact_name ?: $chat->contact_phone ?: 'C', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline gap-3">
                                    <p class="font-bold text-ink truncate" data-field="name">
                                        {{ $chat->clienteFinal?->nome_responsavel ?: ($chat->contact_name ?: $chat->contact_phone) }}
                                    </p>
                                    <span class="text-xs text-slate shrink-0" data-field="time">
                                        {{ $chat->last_message_at?->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate truncate mt-0.5" data-field="last">
                                    {{ $chat->latestMessage?->content ?: '—' }}
                                </p>
                            </div>
                            <span data-field="unread" class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-2 bg-emerald-600 text-white rounded-full text-xs font-bold {{ $chat->unread_count > 0 ? '' : 'hidden' }}">
                                {{ $chat->unread_count }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
            @if(!$chats->isEmpty())
                <div class="px-5 py-4 border-t border-black/5">
                    {{ $chats->links() }}
                </div>
            @endif
        </div>

        <p class="text-xs text-slate text-center">
            <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full animate-pulse mr-1"></span>
            Atualizando automaticamente a cada 5 segundos
        </p>
    </div>

    <script>
        (function () {
            const list = document.getElementById('inbox-list');
            const empty = document.getElementById('inbox-empty');
            const feedUrl = '{{ route('customer.whatsapp.inbox.feed') }}';

            function render(chats) {
                if (!chats.length) {
                    list.classList.add('hidden');
                    empty.classList.remove('hidden');
                    return;
                }
                empty.classList.add('hidden');
                list.classList.remove('hidden');

                const existing = new Map();
                list.querySelectorAll('li[data-chat-id]').forEach(li => existing.set(li.dataset.chatId, li));

                const frag = document.createDocumentFragment();
                chats.forEach(c => {
                    const li = existing.get(String(c.id));
                    if (li) {
                        li.querySelector('[data-field="name"]').textContent = c.name || c.phone;
                        li.querySelector('[data-field="time"]').textContent = c.last_at_human || '';
                        li.querySelector('[data-field="last"]').textContent = c.last_text || '—';
                        const unread = li.querySelector('[data-field="unread"]');
                        if (c.unread > 0) {
                            unread.textContent = c.unread;
                            unread.classList.remove('hidden');
                        } else {
                            unread.classList.add('hidden');
                        }
                        frag.appendChild(li);
                    } else {
                        const nli = document.createElement('li');
                        nli.dataset.chatId = c.id;
                        nli.innerHTML = `
                            <a href="${c.url}" class="flex items-center gap-4 px-5 py-4 hover:bg-mist transition-colors">
                                <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center">
                                    ${(c.name || c.phone || 'C').charAt(0).toUpperCase()}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline gap-3">
                                        <p class="font-bold text-ink truncate" data-field="name">${escapeHtml(c.name || c.phone)}</p>
                                        <span class="text-xs text-slate shrink-0" data-field="time">${c.last_at_human || ''}</span>
                                    </div>
                                    <p class="text-sm text-slate truncate mt-0.5" data-field="last">${escapeHtml(c.last_text || '—')}</p>
                                </div>
                                <span data-field="unread" class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-2 bg-emerald-600 text-white rounded-full text-xs font-bold ${c.unread > 0 ? '' : 'hidden'}">
                                    ${c.unread || 0}
                                </span>
                            </a>`;
                        frag.appendChild(nli);
                    }
                });
                list.innerHTML = '';
                list.appendChild(frag);
            }

            function escapeHtml(s) {
                return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
            }

            async function poll() {
                try {
                    const res = await fetch(feedUrl, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                    if (!res.ok) return;
                    const data = await res.json();
                    render(data.chats);
                } catch (e) {}
            }

            setInterval(poll, 5000);
        })();
    </script>
</x-app-layout>
