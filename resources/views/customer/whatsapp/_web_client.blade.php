@php
    /** @var string $routePrefix  e.g. 'customer.whatsapp.' or 'admin.hub.whatsapp.' */
    /** @var \App\Models\WhatsappInstance|null $instance */
    /** @var array $chatsData */
    /** @var \App\Models\WhatsappChat|null $activeChat */
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|null $messages */

    $activeChat = $activeChat ?? ($chat ?? null);
    $messagesList = isset($messages)
        ? collect($messages instanceof \Illuminate\Contracts\Pagination\Paginator ? $messages->items() : $messages)
        : collect();
    $latestMessageId = (int) ($messagesList->last()?->id ?? 0);
    $isConnected = $instance && $instance->isConnected();
@endphp

<div
    x-data="whatsappWebApp({
        initialChats: @js($chatsData ?? []),
        feedInboxUrl: @js(route($routePrefix . 'inbox.feed')),
        activeChatId: @js($activeChat?->id),
        feedChatUrl: @js($activeChat ? route($routePrefix . 'chat.feed', $activeChat) : null),
        sendUrl: @js($activeChat ? route($routePrefix . 'send', $activeChat) : null),
        latestId: @js($latestMessageId),
        botActive: @js((bool) ($activeChat?->is_bot_active ?? true)),
        csrfToken: @js(csrf_token()),
    })"
    x-init="init()"
    @keydown.escape.window="fullscreen && toggleFullscreen()"
    :class="fullscreen ? 'fixed inset-0 z-[100] h-screen rounded-none border-0' : 'h-[calc(100vh-24px)] max-md:h-[calc(100vh-90px)] rounded-2xl border border-[#D1D7DB]'"
    class="flex flex-col bg-white shadow-lg overflow-hidden min-h-[560px]"
>
    {{-- Top WhatsApp Web Emerald Bar --}}
    <div class="h-1.5 w-full bg-gradient-to-r from-[#00A884] via-[#25D366] to-[#008069] shrink-0"></div>

    <div class="flex flex-1 min-h-0 overflow-hidden">
        {{-- ====================================================================
             LEFT PANE: CONVERSATION LIST (WhatsApp Web Sidebar)
             ==================================================================== --}}
        <aside class="{{ $activeChat ? 'hidden md:flex' : 'flex' }} w-full md:w-[350px] lg:w-[400px] shrink-0 flex-col border-r border-[#E9EDEF] bg-white select-none">
            {{-- Left Header (#F0F2F5) --}}
            <div class="bg-[#F0F2F5] px-4 py-2.5 border-b border-[#E9EDEF] flex items-center justify-between gap-2 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <div class="w-10 h-10 rounded-full bg-[#00A884] text-white flex items-center justify-center font-bold text-sm shadow-sm">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-[#F0F2F5] {{ $isConnected ? 'bg-[#25D366]' : 'bg-amber-500' }}"
                              title="{{ $isConnected ? 'WhatsApp Conectado' : 'WhatsApp Desconectado' }}"></span>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h2 class="text-sm font-bold text-[#111B21] truncate">WhatsApp Web</h2>
                            <span x-show="unreadTotal > 0"
                                  x-text="unreadTotal"
                                  class="px-1.5 py-0.2 rounded-full text-[11px] font-bold bg-[#25D366] text-white leading-tight"></span>
                        </div>
                        <p class="text-[11px] text-[#54656F] truncate flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full" :class="syncing ? 'bg-[#00A884] animate-ping' : 'bg-[#25D366]'"></span>
                            <span x-text="syncing ? 'Sincronizando...' : '{{ $isConnected ? 'Online • Tempo real' : 'Desconectado' }}'"></span>
                        </p>
                    </div>
                </div>

                {{-- Header Action Icons --}}
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button"
                            @click="toggleFullscreen()"
                            :title="fullscreen ? 'Sair da tela cheia (Esc)' : 'Tela cheia'"
                            class="p-2 rounded-full text-[#54656F] hover:text-[#111B21] hover:bg-[#E9EDEF] transition cursor-pointer">
                        <svg x-show="!fullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/>
                        </svg>
                        <svg x-show="fullscreen" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4v4H4M16 4v4h4M8 20v-4H4M16 20v-4h4"/>
                        </svg>
                    </button>
                    <a href="{{ route($routePrefix . 'bot.index') }}"
                       title="Configurar Agente Bruce IA"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-[#54656F] hover:text-[#111B21] hover:bg-[#E9EDEF] transition">
                        <span>🤖</span>
                        <span class="hidden xl:inline">Bot IA</span>
                    </a>
                    <a href="{{ route($routePrefix . 'conexao') }}"
                       title="Status da Conexão & QR Code"
                       class="p-2 rounded-full text-[#54656F] hover:text-[#111B21] hover:bg-[#E9EDEF] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Disconnected Warning Strip --}}
            @if(!$isConnected)
                <div class="bg-amber-50 border-b border-amber-200 px-3.5 py-2 flex items-center justify-between gap-2 text-xs text-amber-900 shrink-0">
                    <span class="flex items-center gap-1.5 font-medium truncate">
                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                        Instância desconectada
                    </span>
                    <a href="{{ route($routePrefix . 'conexao') }}" class="font-bold text-amber-800 underline shrink-0 hover:text-amber-950">
                        Conectar QR
                    </a>
                </div>
            @endif

            {{-- Search & Filter Pills Bar --}}
            <div class="px-3 py-2 bg-white border-b border-[#F0F2F5] space-y-2 shrink-0">
                <div class="relative flex items-center bg-[#F0F2F5] rounded-lg px-3 py-1.5 focus-within:bg-white focus-within:ring-1 focus-within:ring-[#00A884] transition">
                    <svg class="w-4 h-4 text-[#54656F] shrink-0 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                    <input type="text"
                           x-model="search"
                           placeholder="Pesquisar ou começar uma nova conversa"
                           class="w-full bg-transparent border-0 p-0 text-[13px] text-[#111B21] placeholder-[#667781] focus:ring-0">
                    <button type="button"
                            x-show="search.length > 0"
                            @click="search = ''"
                            class="text-[#667781] hover:text-[#111B21] ml-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Filter Chips (WhatsApp Web style) --}}
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-0.5">
                    <button type="button"
                            @click="filter = 'all'"
                            :class="filter === 'all' ? 'bg-[#E7FCE3] text-[#008069] font-semibold' : 'bg-[#F0F2F5] text-[#54656F] hover:bg-[#E9EDEF]'"
                            class="px-3 py-1 rounded-full text-xs transition shrink-0">
                        Tudo
                    </button>
                    <button type="button"
                            @click="filter = 'unread'"
                            :class="filter === 'unread' ? 'bg-[#E7FCE3] text-[#008069] font-semibold' : 'bg-[#F0F2F5] text-[#54656F] hover:bg-[#E9EDEF]'"
                            class="px-3 py-1 rounded-full text-xs transition shrink-0 flex items-center gap-1">
                        <span>Não lidas</span>
                        <span x-show="unreadTotal > 0" x-text="unreadTotal" class="text-[10px] font-bold"></span>
                    </button>
                    <button type="button"
                            @click="filter = 'paused'"
                            :class="filter === 'paused' ? 'bg-[#FEF3C7] text-amber-800 font-semibold' : 'bg-[#F0F2F5] text-[#54656F] hover:bg-[#E9EDEF]'"
                            class="px-3 py-1 rounded-full text-xs transition shrink-0">
                        ⏸ Humano
                    </button>
                    <button type="button"
                            @click="filter = 'bot'"
                            :class="filter === 'bot' ? 'bg-[#E7FCE3] text-[#008069] font-semibold' : 'bg-[#F0F2F5] text-[#54656F] hover:bg-[#E9EDEF]'"
                            class="px-3 py-1 rounded-full text-xs transition shrink-0">
                        🤖 Bot IA
                    </button>
                </div>
            </div>

            {{-- Conversation List --}}
            <div class="flex-1 overflow-y-auto divide-y divide-[#F0F2F5]">
                <template x-if="filteredChats.length === 0">
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-[#F0F2F5] text-[#667781] flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-[#111B21]">Nenhuma conversa encontrada</p>
                        <p class="text-xs text-[#667781] mt-1">As mensagens recebidas aparecerão automaticamente aqui.</p>
                    </div>
                </template>

                <template x-for="c in filteredChats" :key="c.id">
                    <a :href="c.url"
                       class="flex items-center gap-3 px-3.5 py-3 transition cursor-pointer relative group"
                       :class="c.id === activeChatId ? 'bg-[#F0F2F5]' : 'hover:bg-[#F5F6F6]'">
                        {{-- Active Left Indicator Bar --}}
                        <span x-show="c.id === activeChatId"
                              class="absolute left-0 top-0 bottom-0 w-1 bg-[#00A884]"></span>

                        {{-- Contact Avatar --}}
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-base text-white shadow-xs"
                                 :style="avatarStyle(c.name)"
                                 x-text="initials(c.name)"></div>
                            {{-- Bot Status Mini Badge --}}
                            <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full border-2 border-white flex items-center justify-center text-[9px]"
                                  :class="c.bot_active ? 'bg-[#25D366] text-white' : 'bg-amber-400 text-amber-950'"
                                  :title="c.bot_active ? 'Bot IA Ativo nesta conversa' : 'Bot Pausado (Atendimento Humano)'"
                                  x-text="c.bot_active ? '🤖' : '⏸'"></span>
                        </div>

                        {{-- Chat Summary --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-[15px] text-[#111B21] truncate"
                                   :class="c.unread > 0 ? 'font-bold' : 'font-medium'"
                                   x-text="c.name"></p>
                                <span class="text-[11px] shrink-0"
                                      :class="c.unread > 0 ? 'text-[#00A884] font-bold' : 'text-[#667781]'"
                                      x-text="formatChatTime(c.last_at_iso)"></span>
                            </div>

                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <p class="text-[13px] truncate flex items-center gap-1"
                                   :class="c.unread > 0 ? 'text-[#111B21] font-semibold' : 'text-[#667781]'">
                                    {{-- Outbound Double Check --}}
                                    <template x-if="c.last_direction === 'outbound'">
                                        <svg class="w-4 h-4 text-[#53BDEB] shrink-0 inline" viewBox="0 0 16 15" fill="none">
                                            <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="currentColor"/>
                                        </svg>
                                    </template>
                                    <span class="truncate" x-text="previewText(c)"></span>
                                </p>

                                <div class="flex items-center gap-1 shrink-0">
                                    <span x-show="!c.bot_active"
                                          class="px-1.5 py-0.5 text-[10px] rounded bg-amber-100 text-amber-800 font-semibold">
                                        Humano
                                    </span>
                                    <span x-show="c.unread > 0"
                                          x-text="c.unread"
                                          class="min-w-[20px] h-5 px-1.5 rounded-full bg-[#25D366] text-white text-[11px] font-bold flex items-center justify-center"></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </aside>

        {{-- ====================================================================
             RIGHT PANE: ACTIVE CONVERSATION OR WHATSAPP WEB EMPTY STATE
             ==================================================================== --}}
        @if($activeChat)
            @php
                $contactName = $activeChat->clienteFinal?->nome_responsavel
                    ?: ($activeChat->contact_name ?: ($activeChat->contact_phone ?: 'Contato sem nome'));
                $contactPhone = $activeChat->contact_phone ?: $activeChat->wa_id;
            @endphp
            <section class="flex-1 flex flex-col bg-[#EFEAE2] min-w-0 relative">
                {{-- Active Chat Header (#F0F2F5) --}}
                <header class="bg-[#F0F2F5] px-4 py-2.5 border-b border-[#E9EDEF] flex items-center justify-between gap-3 shrink-0 z-10">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Mobile Back Button --}}
                        <a href="{{ route($routePrefix . 'inbox') }}"
                           class="md:hidden p-1.5 -ml-1 rounded-full text-[#54656F] hover:bg-[#E9EDEF]"
                           title="Voltar para conversas">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>

                        {{-- Contact Avatar --}}
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white shrink-0 shadow-xs"
                             :style="avatarStyle(@js($contactName))">
                            {{ mb_strtoupper(mb_substr($contactName, 0, 2)) }}
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-[15px] font-bold text-[#111B21] truncate">{{ $contactName }}</h3>
                                @if($activeChat->clienteFinal && Route::has('customer.crm.show'))
                                    <a href="{{ route('customer.crm.show', $activeChat->clienteFinal) }}"
                                       class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-[#008069] hover:bg-emerald-200 transition">
                                        Ver no CRM →
                                    </a>
                                @endif
                            </div>
                            <p class="text-xs text-[#667781] truncate flex items-center gap-2">
                                <span>+{{ $contactPhone }}</span>
                                <span>•</span>
                                <span x-show="botActive" class="text-[#008069] font-medium">🤖 Bruce IA respondendo automaticamente</span>
                                <span x-show="!botActive" class="text-amber-700 font-medium">⏸ IA pausada • Atendimento humano</span>
                            </p>
                        </div>
                    </div>

                    {{-- Right Header Controls: Bot IA Toggle --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route($routePrefix . 'bot.toggle', $activeChat) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-xs cursor-pointer"
                                    :class="botActive
                                        ? 'bg-white text-[#111B21] border border-[#D1D7DB] hover:bg-amber-50 hover:border-amber-300 hover:text-amber-900'
                                        : 'bg-[#00A884] text-white hover:bg-[#008F6F]'">
                                <span x-text="botActive ? '⏸ Assumir conversa (Pausar IA)' : '🤖 Reativar Bruce IA'"></span>
                            </button>
                        </form>
                    </div>
                </header>

                {{-- Session / AJAX Error Notifications --}}
                @if(session('error'))
                    <div class="bg-red-50 border-b border-red-200 px-4 py-2 text-xs text-red-800 flex items-center justify-between z-10">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                <div x-show="sendError" x-cloak
                     class="bg-red-50 border-b border-red-200 px-4 py-2 text-xs text-red-800 flex items-center justify-between z-10">
                    <span x-text="sendError"></span>
                    <button type="button" @click="sendError = null" class="font-bold underline ml-2">Fechar</button>
                </div>

                {{-- Messages Viewport with Subtle WhatsApp Doodle Background --}}
                <div x-ref="viewport"
                     class="flex-1 overflow-y-auto px-4 sm:px-8 lg:px-14 py-5 space-y-2 relative"
                     style="background-color: #EFEAE2; background-image: radial-gradient(#CBD5E1 0.75px, transparent 0.75px); background-size: 20px 20px;">

                    {{-- WhatsApp Encryption / Integration Banner --}}
                    <div class="flex justify-center mb-4">
                        <div class="max-w-md bg-[#FFEECD] text-[#54656F] text-[11.5px] px-3.5 py-1.5 rounded-lg shadow-2xs text-center leading-relaxed">
                            🔒 Conversa integrada ao WhatsApp oficial da empresa. Ao enviar uma mensagem manual, você pode pausar o <strong>Bruce IA</strong> no topo se desejar assumir o atendimento.
                        </div>
                    </div>

                    @if($messagesList->isEmpty())
                        <div x-show="newMessages.length === 0" class="text-center py-12">
                            <span class="inline-block bg-white/90 text-[#54656F] text-xs px-4 py-2 rounded-full shadow-xs">
                                Nenhuma mensagem registrada ainda. Envie um "Olá" abaixo!
                            </span>
                        </div>
                    @endif

                    {{-- Server-Rendered Messages --}}
                    @php $lastDate = null; @endphp
                    @foreach($messagesList as $m)
                        @php
                            $msgDate = $m->created_at->format('d/m/Y');
                            if ($m->created_at->isToday()) {
                                $msgDateLabel = 'Hoje';
                            } elseif ($m->created_at->isYesterday()) {
                                $msgDateLabel = 'Ontem';
                            } else {
                                $msgDateLabel = $msgDate;
                            }
                            $isOut = $m->direction === 'outbound';
                        @endphp

                        @if($lastDate !== $msgDate)
                            <div class="flex justify-center my-3">
                                <span class="bg-white/95 text-[#54656F] text-[11px] font-semibold px-3 py-1 rounded-lg shadow-2xs uppercase tracking-wider">
                                    {{ $msgDateLabel }}
                                </span>
                            </div>
                            @php $lastDate = $msgDate; @endphp
                        @endif

                        <div class="flex {{ $isOut ? 'justify-end' : 'justify-start' }}">
                            <div class="relative max-w-[82%] sm:max-w-[70%] rounded-lg px-3 py-1.5 shadow-xs {{ $isOut ? 'bg-[#D9FDD3] text-[#111B21] rounded-tr-none' : 'bg-white text-[#111B21] rounded-tl-none' }}">
                                @if($m->type !== 'chat')
                                    <div class="text-[11px] font-semibold uppercase tracking-wide mb-1 flex items-center gap-1 {{ $isOut ? 'text-[#008069]' : 'text-[#54656F]' }}">
                                        <span>📎 {{ $m->type }}</span>
                                    </div>
                                @endif

                                @if($m->content)
                                    <div class="text-[14.2px] leading-[19px] whitespace-pre-wrap break-words pr-12">{{ $m->content }}</div>
                                @endif

                                @if($m->media_caption)
                                    <div class="text-xs mt-1 italic text-[#54656F]">{{ $m->media_caption }}</div>
                                @endif

                                <div class="flex items-center justify-end gap-1 -mt-1 select-none">
                                    <span class="text-[10.5px] text-[#667781]">{{ $m->created_at->format('H:i') }}</span>
                                    @if($isOut)
                                        @if($m->status === 'failed')
                                            <span class="text-red-600 text-xs font-bold" title="Falha no envio">⚠</span>
                                        @else
                                            <svg class="w-4 h-3.5 {{ $m->status === 'read' ? 'text-[#53BDEB]' : 'text-[#667781]' }}" viewBox="0 0 16 15" fill="none">
                                                <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="currentColor"/>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Live Dynamic Messages (Polling & Instant Send) --}}
                    <template x-for="m in newMessages" :key="m.id">
                        <div class="flex" :class="m.direction === 'outbound' ? 'justify-end' : 'justify-start'">
                            <div class="relative max-w-[82%] sm:max-w-[70%] rounded-lg px-3 py-1.5 shadow-xs"
                                 :class="m.direction === 'outbound'
                                    ? 'bg-[#D9FDD3] text-[#111B21] rounded-tr-none'
                                    : 'bg-white text-[#111B21] rounded-tl-none'">
                                <template x-if="m.type !== 'chat'">
                                    <div class="text-[11px] font-semibold uppercase tracking-wide mb-1 text-[#008069]"
                                         x-text="'📎 ' + m.type"></div>
                                </template>
                                <template x-if="m.content">
                                    <div class="text-[14.2px] leading-[19px] whitespace-pre-wrap break-words pr-12" x-text="m.content"></div>
                                </template>
                                <template x-if="m.caption">
                                    <div class="text-xs mt-1 italic text-[#54656F]" x-text="m.caption"></div>
                                </template>
                                <div class="flex items-center justify-end gap-1 -mt-1 select-none">
                                    <span class="text-[10.5px] text-[#667781]" x-text="m.time"></span>
                                    <template x-if="m.direction === 'outbound'">
                                        <span>
                                            <template x-if="m.status === 'failed'">
                                                <span class="text-red-600 text-xs font-bold">⚠</span>
                                            </template>
                                            <template x-if="m.status !== 'failed'">
                                                <svg class="w-4 h-3.5 text-[#53BDEB]" viewBox="0 0 16 15" fill="none">
                                                    <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="currentColor"/>
                                                </svg>
                                            </template>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Quick Reply Suggestions Bar --}}
                <div class="bg-[#F0F2F5]/90 px-4 pt-2 pb-1 border-t border-[#E9EDEF] flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0">
                    <span class="text-[11px] font-semibold text-[#667781] shrink-0 mr-1">Respostas rápidas:</span>
                    <button type="button" @click="insertQuickReply('Olá! Tudo bem? Como posso ajudar você hoje? 😊')"
                            class="px-2.5 py-1 rounded-full bg-white hover:bg-[#E7FCE3] text-[11.5px] text-[#111B21] border border-[#D1D7DB] transition shrink-0 cursor-pointer">
                        👋 Boas-vindas
                    </button>
                    <button type="button" @click="insertQuickReply('Perfeito! Já confirmei seu agendamento aqui na nossa agenda. 📅✅')"
                            class="px-2.5 py-1 rounded-full bg-white hover:bg-[#E7FCE3] text-[11.5px] text-[#111B21] border border-[#D1D7DB] transition shrink-0 cursor-pointer">
                        📅 Confirmar horário
                    </button>
                    <button type="button" @click="insertQuickReply('Só um instante, já vou verificar os detalhes para você! 🙏')"
                            class="px-2.5 py-1 rounded-full bg-white hover:bg-[#E7FCE3] text-[11.5px] text-[#111B21] border border-[#D1D7DB] transition shrink-0 cursor-pointer">
                        ⏳ Aguardar um instante
                    </button>
                    <button type="button" @click="insertQuickReply('Muito obrigado pelo contato! Qualquer dúvida estamos à disposição. 🤝')"
                            class="px-2.5 py-1 rounded-full bg-white hover:bg-[#E7FCE3] text-[11.5px] text-[#111B21] border border-[#D1D7DB] transition shrink-0 cursor-pointer">
                        🤝 Agradecimento
                    </button>
                </div>

                {{-- Bottom Message Composer (#F0F2F5) --}}
                <footer class="bg-[#F0F2F5] px-4 py-2.5 shrink-0 z-10">
                    <form method="POST"
                          x-ref="composerForm"
                          action="{{ route($routePrefix . 'send', $activeChat) }}"
                          @submit.prevent="sendMessage($event)"
                          class="flex items-end gap-2.5">
                        @csrf

                        {{-- Quick Emoji Button --}}
                        <div class="relative" x-data="{ emojiOpen: false }">
                            <button type="button"
                                    @click="emojiOpen = !emojiOpen"
                                    class="p-2.5 rounded-full text-[#54656F] hover:text-[#111B21] hover:bg-[#E9EDEF] transition cursor-pointer"
                                    title="Inserir emoji">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                            <div x-show="emojiOpen"
                                 @click.away="emojiOpen = false"
                                 x-cloak
                                 class="absolute bottom-12 left-0 bg-white rounded-xl shadow-xl border border-gray-200 p-2 flex items-center gap-1.5 z-20">
                                <template x-for="em in ['😊','👍','✅','📅','🔥','🙏','👋','🎉','❤️','🤝']" :key="em">
                                    <button type="button"
                                            @click="messageText += em; emojiOpen = false; $refs.composerInput.focus()"
                                            class="w-8 h-8 rounded-lg hover:bg-gray-100 text-lg flex items-center justify-center transition cursor-pointer"
                                            x-text="em"></button>
                                </template>
                            </div>
                        </div>

                        {{-- Textarea Input --}}
                        <div class="flex-1 bg-white rounded-xl border border-white focus-within:border-[#00A884] shadow-2xs px-3.5 py-2">
                            <textarea name="content"
                                      x-ref="composerInput"
                                      x-model="messageText"
                                      rows="1"
                                      required
                                      maxlength="4000"
                                      @keydown.enter.exact.prevent="sendMessage($event)"
                                      placeholder="{{ $isConnected ? 'Digite uma mensagem...' : 'Conecte o WhatsApp para enviar mensagens...' }}"
                                      {{ $isConnected ? '' : 'disabled' }}
                                      class="w-full bg-transparent border-0 p-0 text-[15px] text-[#111B21] placeholder-[#667781] focus:ring-0 resize-none max-h-32 leading-relaxed"></textarea>
                        </div>

                        {{-- Send Button --}}
                        <button type="submit"
                                :disabled="sending || !messageText.trim() || {{ $isConnected ? 'false' : 'true' }}"
                                class="w-11 h-11 rounded-full bg-[#00A884] hover:bg-[#008F6F] disabled:opacity-50 text-white flex items-center justify-center shadow-sm transition shrink-0 cursor-pointer">
                            <svg x-show="!sending" class="w-5 h-5 translate-x-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                            <svg x-show="sending" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                        </button>
                    </form>
                </footer>
            </section>
        @else
            {{-- ================================================================
                 WHATSAPP WEB DESKTOP WELCOME STATE (When no chat is open)
                 ================================================================ --}}
            <section class="hidden md:flex flex-1 flex-col items-center justify-center bg-[#F0F2F5] border-b-[6px] border-[#25D366] p-8 text-center select-none">
                <div class="max-w-md">
                    <div class="w-24 h-24 rounded-full bg-[#E7FCE3] text-[#00A884] flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="fa-brands fa-whatsapp text-5xl"></i>
                    </div>

                    <h2 class="text-2xl font-light text-[#41525D] tracking-tight">
                        WhatsApp Web <span class="font-bold text-[#00A884]">NC5</span>
                    </h2>
                    <p class="text-sm text-[#667781] mt-3 leading-relaxed">
                        Envie e receba mensagens em tempo real enquanto o <strong>Agente Bruce IA</strong> qualifica leads, responde dúvidas e agenda compromissos 24h por dia.
                    </p>

                    {{-- Quick Status Pills --}}
                    <div class="mt-6 grid grid-cols-3 gap-3 text-left">
                        <div class="bg-white rounded-xl p-3 border border-[#E9EDEF] shadow-2xs">
                            <p class="text-[11px] font-semibold text-[#667781] uppercase">Conexão</p>
                            <p class="text-xs font-bold mt-1 flex items-center gap-1.5 {{ $isConnected ? 'text-[#008069]' : 'text-amber-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $isConnected ? 'bg-[#25D366]' : 'bg-amber-500' }}"></span>
                                {{ $isConnected ? 'Ativa' : 'Pendente' }}
                            </p>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-[#E9EDEF] shadow-2xs">
                            <p class="text-[11px] font-semibold text-[#667781] uppercase">Conversas</p>
                            <p class="text-sm font-bold text-[#111B21] mt-0.5" x-text="chats.length"></p>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-[#E9EDEF] shadow-2xs">
                            <p class="text-[11px] font-semibold text-[#667781] uppercase">Não Lidas</p>
                            <p class="text-sm font-bold text-[#00A884] mt-0.5" x-text="unreadTotal"></p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route($routePrefix . 'bot.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#00A884] text-white text-xs font-bold hover:bg-[#008F6F] transition shadow-xs">
                            🤖 Configurar Agente Bruce IA
                        </a>
                        <a href="{{ route($routePrefix . 'conexao') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white text-[#111B21] border border-[#D1D7DB] text-xs font-semibold hover:bg-gray-50 transition">
                            📱 Status & QR Code
                        </a>
                    </div>

                    <p class="text-[11px] text-[#8696A0] mt-8 flex items-center justify-center gap-1.5">
                        <span>🔒</span> Selecione qualquer conversa na coluna à esquerda para abrir o chat.
                    </p>
                </div>
            </section>
        @endif
    </div>
</div>

<script>
    if (typeof window.whatsappWebApp !== 'function') {
        window.whatsappWebApp = function (config) {
            return {
                chats: config.initialChats || [],
                feedInboxUrl: config.feedInboxUrl,
                activeChatId: config.activeChatId || null,
                feedChatUrl: config.feedChatUrl || null,
                sendUrl: config.sendUrl || null,
                latestId: config.latestId || 0,
                botActive: !!config.botActive,
                csrfToken: config.csrfToken || '',

                search: '',
                filter: 'all',
                syncing: false,
                sending: false,
                sendError: null,
                messageText: '',
                newMessages: [],
                inboxTimer: null,
                chatTimer: null,
                fullscreen: localStorage.getItem('nc5_wa_fullscreen') === '1',

                toggleFullscreen() {
                    this.fullscreen = !this.fullscreen;
                    localStorage.setItem('nc5_wa_fullscreen', this.fullscreen ? '1' : '0');
                    this.scrollBottom();
                },

                get unreadTotal() {
                    return this.chats.reduce((acc, c) => acc + (parseInt(c.unread, 10) || 0), 0);
                },

                get filteredChats() {
                    const q = this.search.trim().toLowerCase();
                    return this.chats.filter(c => {
                        if (this.filter === 'unread' && !(c.unread > 0)) return false;
                        if (this.filter === 'paused' && c.bot_active) return false;
                        if (this.filter === 'bot' && !c.bot_active) return false;
                        if (!q) return true;
                        return (c.name && c.name.toLowerCase().includes(q))
                            || (c.phone && String(c.phone).toLowerCase().includes(q))
                            || (c.last_text && c.last_text.toLowerCase().includes(q));
                    });
                },

                init() {
                    this.scrollBottom();
                    this.inboxTimer = setInterval(() => this.pollInbox(), 5000);
                    if (this.feedChatUrl) {
                        this.chatTimer = setInterval(() => this.pollChat(), 3000);
                    }
                },

                scrollBottom() {
                    this.$nextTick(() => {
                        const vp = this.$refs.viewport;
                        if (vp) vp.scrollTop = vp.scrollHeight;
                    });
                },

                initials(name) {
                    if (!name) return 'WA';
                    const parts = String(name).trim().split(/\s+/);
                    if (parts.length >= 2) {
                        return (parts[0][0] + parts[1][0]).toUpperCase();
                    }
                    return String(name).substring(0, 2).toUpperCase();
                },

                avatarStyle(name) {
                    const palette = ['#00A884', '#0284C7', '#7C3AED', '#EA580C', '#0D9488', '#2563EB', '#DB2777'];
                    let hash = 0;
                    const str = String(name || 'WA');
                    for (let i = 0; i < str.length; i++) {
                        hash = str.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    const color = palette[Math.abs(hash) % palette.length];
                    return `background-color: ${color};`;
                },

                previewText(c) {
                    if (c.last_text) return c.last_text;
                    if (c.last_type && c.last_type !== 'chat') return `[${c.last_type}]`;
                    return 'Sem mensagens';
                },

                formatChatTime(iso) {
                    if (!iso) return '';
                    const d = new Date(iso);
                    if (isNaN(d.getTime())) return '';
                    const now = new Date();
                    const sameDay = d.toDateString() === now.toDateString();
                    if (sameDay) {
                        return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                    }
                    const yesterday = new Date();
                    yesterday.setDate(now.getDate() - 1);
                    if (d.toDateString() === yesterday.toDateString()) {
                        return 'Ontem';
                    }
                    return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                },

                insertQuickReply(text) {
                    this.messageText = text;
                    if (this.$refs.composerInput) {
                        this.$refs.composerInput.focus();
                    }
                },

                async sendMessage(event) {
                    const content = this.messageText.trim();
                    if (!content || this.sending) return;

                    const form = this.$refs.composerForm || (event && event.target && event.target.tagName === 'FORM' ? event.target : null);

                    if (!this.sendUrl) {
                        if (form) form.submit();
                        return;
                    }

                    this.sending = true;
                    this.sendError = null;

                    try {
                        const res = await fetch(this.sendUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({ content }),
                        });

                        const data = await res.json().catch(() => null);

                        if (!res.ok || !data || !data.ok) {
                            const err = (data && data.error) ? data.error : ('Erro ao enviar mensagem (HTTP ' + res.status + ')');
                            this.sendError = err;
                            if (data && data.message && !this.newMessages.some(x => x.id === data.message.id)) {
                                this.newMessages.push(data.message);
                                this.latestId = Math.max(this.latestId, data.message.id);
                                this.scrollBottom();
                            }
                        } else if (data.message) {
                            this.messageText = '';
                            if (!this.newMessages.some(x => x.id === data.message.id)) {
                                this.newMessages.push(data.message);
                            }
                            this.latestId = Math.max(this.latestId, data.message.id);
                            this.scrollBottom();
                            this.pollInbox();
                        }
                    } catch (e) {
                        console.error('Falha no envio via AJAX:', e);
                        if (form) {
                            form.submit();
                        } else {
                            this.sendError = 'Erro de conexão ao tentar enviar a mensagem.';
                        }
                    } finally {
                        this.sending = false;
                    }
                },

                async pollInbox() {
                    if (document.hidden) return;
                    this.syncing = true;
                    try {
                        const res = await fetch(this.feedInboxUrl, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (Array.isArray(data.chats)) {
                            this.chats = data.chats;
                        }
                    } catch (e) {
                        // ignore transient errors
                    } finally {
                        this.syncing = false;
                    }
                },

                async pollChat() {
                    if (document.hidden || !this.feedChatUrl) return;
                    try {
                        const url = `${this.feedChatUrl}?after_id=${this.latestId}`;
                        const res = await fetch(url, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (typeof data.bot_active === 'boolean') {
                            this.botActive = data.bot_active;
                        }
                        if (Array.isArray(data.messages) && data.messages.length > 0) {
                            for (const m of data.messages) {
                                if (!this.newMessages.some(x => x.id === m.id)) {
                                    this.newMessages.push(m);
                                }
                            }
                            this.latestId = data.latest_id || this.latestId;
                            this.scrollBottom();
                        }
                    } catch (e) {
                        // ignore transient errors
                    }
                }
            };
        };
    }
</script>
