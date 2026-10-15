<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">Inbox</h2>
                <p class="text-slate text-sm mt-1">Mensagens recebidas no número conectado — tudo em um lugar.</p>
            </div>
            <div class="flex items-center gap-3">
                @if($instance?->isConnected())
                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-1.5 rounded-full text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Conectado
                    </div>
                @else
                    <a href="{{ route('customer.whatsapp.conexao') }}"
                       class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 px-3 py-1.5 rounded-full text-xs font-bold hover:bg-amber-100">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Desconectado · conectar
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div x-data="inboxApp()" x-init="init()" class="space-y-4">
        {{-- Barra de busca + filtros --}}
        <div class="bg-white border border-black/5 rounded-2xl shadow-sm p-3 flex items-center gap-3 flex-wrap sticky top-0 z-10">
            <div class="relative flex-1 min-w-[200px]">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model.debounce.200ms="search" placeholder="Buscar por nome ou telefone..."
                       class="w-full pl-9 pr-3 py-2 bg-mist/70 border border-transparent focus:bg-white focus:border-bruce rounded-xl text-sm focus:outline-none transition-colors">
            </div>
            <div class="flex items-center gap-1 bg-mist/70 rounded-xl p-1 text-sm">
                <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-white shadow-sm text-ink' : 'text-slate hover:text-ink'" class="px-3 py-1.5 rounded-lg font-bold transition-all">
                    Todas <span class="text-xs opacity-60" x-text="`(${chats.length})`"></span>
                </button>
                <button @click="filter = 'unread'" :class="filter === 'unread' ? 'bg-white shadow-sm text-ink' : 'text-slate hover:text-ink'" class="px-3 py-1.5 rounded-lg font-bold transition-all">
                    Não lidas <span class="text-xs" :class="unreadTotal > 0 ? 'text-emerald-600 font-bold' : 'opacity-60'" x-text="`(${unreadTotal})`"></span>
                </button>
                <button @click="filter = 'bot_paused'" :class="filter === 'bot_paused' ? 'bg-white shadow-sm text-ink' : 'text-slate hover:text-ink'" class="px-3 py-1.5 rounded-lg font-bold transition-all">
                    Bot pausado
                </button>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-slate" title="Atualiza automaticamente">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span x-text="lastSyncLabel"></span>
            </div>
        </div>

        {{-- Lista --}}
        <div class="bg-white border border-black/5 rounded-3xl shadow-sm overflow-hidden">
            {{-- Empty state --}}
            <template x-if="filteredChats.length === 0">
                <div class="py-20 text-center">
                    <template x-if="chats.length === 0">
                        <div>
                            <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-emerald-50 flex items-center justify-center">
                                <svg class="w-10 h-10 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            </div>
                            <h3 class="font-display font-bold text-xl text-ink">Nenhuma conversa ainda</h3>
                            <p class="text-slate text-sm mt-2 max-w-md mx-auto">Quando alguém mandar mensagem pro seu número conectado, aparece aqui automaticamente.</p>
                        </div>
                    </template>
                    <template x-if="chats.length > 0 && search">
                        <div>
                            <svg class="w-14 h-14 mx-auto mb-4 text-slate/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <p class="text-slate text-sm">Nenhum resultado pra <strong class="text-ink" x-text="`&quot;${search}&quot;`"></strong></p>
                        </div>
                    </template>
                    <template x-if="chats.length > 0 && !search">
                        <div>
                            <p class="text-slate text-sm">Nada no filtro atual.</p>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Lista de chats --}}
            <ul class="divide-y divide-black/5">
                <template x-for="c in filteredChats" :key="c.id">
                    <li>
                        <a :href="c.url"
                           class="flex items-center gap-4 px-4 sm:px-5 py-3.5 hover:bg-mist/60 transition-colors group">
                            {{-- Avatar colorido --}}
                            <div class="relative shrink-0">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white text-base"
                                     :style="`background-color: ${avatarColor(c.name)}`"
                                     x-text="initials(c.name)"></div>
                                {{-- Indicador de bot ativo (ponto emerald no canto) --}}
                                <template x-if="c.bot_active && !c.is_group">
                                    <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full" title="Bot ativo"></span>
                                </template>
                                <template x-if="!c.bot_active && !c.is_group">
                                    <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-slate-300 border-2 border-white rounded-full" title="Bot pausado"></span>
                                </template>
                            </div>

                            {{-- Nome + preview --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-3">
                                    <p class="font-bold text-ink truncate">
                                        <span x-text="c.name"></span>
                                        <template x-if="c.is_group">
                                            <span class="ml-1 text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wider align-middle">Grupo</span>
                                        </template>
                                    </p>
                                    <span class="text-xs shrink-0" :class="c.unread > 0 ? 'text-emerald-600 font-bold' : 'text-slate'" x-text="smartTime(c.last_at_iso)"></span>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    {{-- Ícone de direção --}}
                                    <template x-if="c.last_direction === 'outbound'">
                                        <svg class="w-3.5 h-3.5 text-slate/60 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </template>
                                    <p class="text-sm text-slate truncate flex-1" :class="c.unread > 0 ? 'text-ink font-medium' : ''">
                                        <template x-if="c.last_type && c.last_type !== 'chat'">
                                            <span class="italic opacity-80" x-text="`[${mediaLabel(c.last_type)}] `"></span>
                                        </template>
                                        <span x-text="c.last_text || '—'"></span>
                                    </p>
                                </div>
                                <p class="text-xs text-slate/60 mt-0.5" x-text="c.phone"></p>
                            </div>

                            {{-- Badge unread --}}
                            <template x-if="c.unread > 0">
                                <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-2 bg-emerald-500 text-white rounded-full text-xs font-bold shrink-0" x-text="c.unread"></span>
                            </template>
                        </a>
                    </li>
                </template>
            </ul>
        </div>
    </div>

    <script>
        function inboxApp() {
            return {
                chats: @json($chats->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->clienteFinal?->nome_responsavel ?: ($c->contact_name ?: $c->contact_phone ?: 'Sem nome'),
                    'phone' => $c->contact_phone ?: $c->wa_id,
                    'last_at_iso' => optional($c->last_message_at)->toIso8601String(),
                    'unread' => (int) $c->unread_count,
                    'last_text' => $c->latestMessage?->content ? mb_substr((string) $c->latestMessage->content, 0, 120) : null,
                    'last_direction' => $c->latestMessage?->direction,
                    'last_type' => $c->latestMessage?->type,
                    'bot_active' => (bool) $c->is_bot_active,
                    'is_group' => (bool) $c->is_group,
                    'url' => route('customer.whatsapp.chat', $c),
                ])->values()),
                filter: 'all',
                search: '',
                lastSync: null,
                lastSyncLabel: 'Atualizando...',

                init() {
                    this.updateSyncLabel();
                    setInterval(() => this.poll(), 5000);
                    setInterval(() => this.updateSyncLabel(), 10000);
                },

                get unreadTotal() {
                    return this.chats.reduce((s, c) => s + (c.unread || 0), 0);
                },

                get filteredChats() {
                    let out = this.chats;
                    if (this.filter === 'unread') out = out.filter(c => c.unread > 0);
                    if (this.filter === 'bot_paused') out = out.filter(c => !c.bot_active && !c.is_group);
                    if (this.search.trim()) {
                        const q = this.search.toLowerCase().trim();
                        out = out.filter(c => (c.name || '').toLowerCase().includes(q) || (c.phone || '').includes(q));
                    }
                    return out;
                },

                initials(name) {
                    if (!name) return '?';
                    const parts = name.trim().split(/\s+/);
                    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
                    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
                },

                avatarColor(name) {
                    const palette = ['#059669','#2563eb','#7c3aed','#db2777','#f59e0b','#dc2626','#0891b2','#4f46e5','#ea580c','#65a30d'];
                    const str = String(name || '');
                    let hash = 0;
                    for (let i = 0; i < str.length; i++) hash = (hash * 31 + str.charCodeAt(i)) & 0xffffffff;
                    return palette[Math.abs(hash) % palette.length];
                },

                smartTime(iso) {
                    if (!iso) return '';
                    const d = new Date(iso);
                    const now = new Date();
                    const sameDay = d.toDateString() === now.toDateString();
                    const yest = new Date(now); yest.setDate(yest.getDate() - 1);
                    const isYesterday = d.toDateString() === yest.toDateString();
                    if (sameDay) return d.toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit'});
                    if (isYesterday) return 'Ontem';
                    const diffDays = Math.round((now - d) / (1000*60*60*24));
                    if (diffDays < 7) return ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'][d.getDay()];
                    return d.toLocaleDateString('pt-BR', {day:'2-digit', month:'2-digit'});
                },

                mediaLabel(type) {
                    return {image:'imagem', video:'vídeo', audio:'áudio', document:'documento', sticker:'figurinha'}[type] || type;
                },

                updateSyncLabel() {
                    if (!this.lastSync) { this.lastSyncLabel = 'Carregando...'; return; }
                    const secs = Math.round((new Date() - this.lastSync) / 1000);
                    if (secs < 10) this.lastSyncLabel = 'Atualizado agora';
                    else if (secs < 60) this.lastSyncLabel = `Há ${secs}s`;
                    else this.lastSyncLabel = `Há ${Math.round(secs / 60)}min`;
                },

                async poll() {
                    try {
                        const res = await fetch('{{ route('customer.whatsapp.inbox.feed') }}', {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                        if (!res.ok) return;
                        const data = await res.json();
                        this.chats = data.chats;
                        this.lastSync = new Date();
                        this.updateSyncLabel();
                    } catch (e) {}
                },
            };
        }
    </script>
</x-app-layout>
