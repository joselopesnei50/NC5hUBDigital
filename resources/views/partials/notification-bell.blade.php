@php
    /** @var string $bellPrefix  'admin.hub.' ou 'customer.' */
    /** @var int $bellClienteId */
    /** @var string|null $bellAgendaRoute */
    $bellFeed = \App\Http\Controllers\PanelNotificationController::buildFeed((int) $bellClienteId);
@endphp

<div x-data="panelNotificationBell({
        initial: @js($bellFeed),
        feedUrl: @js(route($bellPrefix . 'notifications.feed')),
        readAllUrl: @js(route($bellPrefix . 'notifications.read-all')),
        dismissUrlTpl: @js(route($bellPrefix . 'notifications.dismiss', ['alerta' => '__ID__'])),
        csrf: @js(csrf_token()),
     })"
     x-init="init()"
     class="relative">
    <button type="button" @click="toggle()"
            class="relative w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer"
            title="Notificações">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span x-show="unread > 0" x-cloak x-text="unread > 9 ? '9+' : unread"
              class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-[#FF7A1A] text-white text-[10px] font-extrabold flex items-center justify-center ring-2 ring-[#0A1128] animate-pulse"></span>
    </button>

    {{-- Painel fixo (evita corte pelo overflow da sidebar) --}}
    <div x-show="open" x-cloak x-transition.opacity
         @click.outside="open = false" @keydown.escape.window="open = false"
         class="fixed z-[200] top-20 left-4 right-4 md:left-[17rem] md:right-auto md:top-6 md:w-[400px] bg-white text-[#0A1128] rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
            <div>
                <p class="font-bold text-sm">Notificações</p>
                <p class="text-[11px] text-slate-500"><span x-text="unread"></span> não lida(s)</p>
            </div>
            <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg text-[11px] font-bold">
                <button type="button" @click="tab = 'all'" :class="tab === 'all' ? 'bg-white shadow-sm text-[#0A1128]' : 'text-slate-500'" class="px-2.5 py-1 rounded-md cursor-pointer">Todas</button>
                <button type="button" @click="tab = 'agenda'" :class="tab === 'agenda' ? 'bg-white shadow-sm text-[#0A1128]' : 'text-slate-500'" class="px-2.5 py-1 rounded-md cursor-pointer">📅 Agenda</button>
            </div>
        </div>

        <div class="max-h-[60vh] overflow-y-auto divide-y divide-slate-100">
            <template x-if="filtered.length === 0">
                <div class="p-8 text-center text-xs text-slate-500">
                    <p class="text-2xl mb-2">🔕</p>
                    Nenhuma notificação por aqui.
                </div>
            </template>
            <template x-for="n in filtered" :key="n.id">
                <div class="px-4 py-3 flex gap-3 group" :class="n.lido ? 'bg-white' : 'bg-orange-50/50'">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base shrink-0"
                         :class="iconBg(n)" x-text="iconFor(n)"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs font-bold leading-snug" x-text="n.titulo"></p>
                            <button type="button" @click="dismiss(n)" title="Dispensar"
                                    class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-rose-600 text-sm leading-none cursor-pointer">&times;</button>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed" x-text="n.mensagem"></p>
                        <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1.5">
                            <span x-show="!n.lido" class="w-1.5 h-1.5 rounded-full bg-[#FF7A1A]"></span>
                            <span x-text="n.quando"></span>
                        </p>
                    </div>
                </div>
            </template>
        </div>

        <div class="px-4 py-2.5 border-t border-slate-100 flex items-center justify-between bg-slate-50">
            <button type="button" @click="markAllRead()" class="text-[11px] font-bold text-slate-600 hover:text-[#0A1128] cursor-pointer">
                ✓ Marcar todas como lidas
            </button>
            @if(!empty($bellAgendaRoute) && Route::has($bellAgendaRoute))
                <a href="{{ route($bellAgendaRoute) }}" class="text-[11px] font-bold text-[#FF7A1A] hover:underline">Abrir Agenda &rarr;</a>
            @endif
        </div>
    </div>
</div>

@once
    <script>
        window.panelNotificationBell = function (cfg) {
            return {
                open: false,
                tab: 'all',
                unread: cfg.initial.unread || 0,
                items: cfg.initial.items || [],
                timer: null,

                init() {
                    this.timer = setInterval(() => this.refresh(), 30000);
                },
                get filtered() {
                    return this.tab === 'agenda' ? this.items.filter(i => i.is_agenda) : this.items;
                },
                toggle() {
                    this.open = !this.open;
                    if (this.open) this.refresh();
                },
                async refresh() {
                    try {
                        const r = await fetch(cfg.feedUrl, { headers: { 'Accept': 'application/json' } });
                        if (!r.ok) return;
                        const d = await r.json();
                        if (d.unread > this.unread && !this.open) this.ping();
                        this.unread = d.unread;
                        this.items = d.items;
                    } catch (e) {}
                },
                async markAllRead() {
                    await this.post(cfg.readAllUrl);
                    this.items = this.items.map(i => ({ ...i, lido: true }));
                    this.unread = 0;
                },
                async dismiss(n) {
                    await this.post(cfg.dismissUrlTpl.replace('__ID__', n.id));
                    if (!n.lido) this.unread = Math.max(0, this.unread - 1);
                    this.items = this.items.filter(i => i.id !== n.id);
                },
                post(url) {
                    return fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': cfg.csrf, 'Accept': 'application/json' },
                    }).catch(() => {});
                },
                ping() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const o = ctx.createOscillator(); const g = ctx.createGain();
                        o.connect(g); g.connect(ctx.destination);
                        o.frequency.value = 880; g.gain.value = 0.05;
                        o.start(); o.stop(ctx.currentTime + 0.15);
                    } catch (e) {}
                },
                iconFor(n) {
                    return ({ agenda_novo: '📅', agenda_confirmado: '✅', agenda_cancelado: '❌' })[n.tipo]
                        || (n.severidade === 'critico' ? '🚨' : (n.severidade === 'atencao' ? '⚠️' : '🔔'));
                },
                iconBg(n) {
                    return ({ agenda_novo: 'bg-blue-100', agenda_confirmado: 'bg-emerald-100', agenda_cancelado: 'bg-rose-100' })[n.tipo]
                        || (n.severidade === 'critico' ? 'bg-rose-100' : 'bg-amber-100');
                },
            };
        };
    </script>
@endonce
