<div class="flex flex-col h-full bg-[#F4F4F5]">
    <!-- Cabeçalho -->
    <div class="relative bg-[#0A0A0B] px-5 py-4 flex items-center gap-3 shrink-0">
        <div class="shrink-0">
            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-11 h-11">
        </div>
        <div class="flex-1 min-w-0 pr-24">
            <h3 class="font-display font-extrabold text-white text-xl leading-none tracking-tight">
                <span class="text-white">Bruce</span><span class="text-[#FF7A1A]">IA</span>
            </h3>
            <p class="text-white/60 text-xs mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Online · Analista de negócios
            </p>
        </div>
        <button
            type="button"
            onclick="return confirm('Encerrar esta conversa e começar uma nova? O histórico atual será arquivado.')"
            wire:click="resetConversation"
            class="absolute top-1/2 -translate-y-1/2 right-14 inline-flex items-center gap-1.5 bg-white/10 hover:bg-[#FF7A1A] text-white text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full transition-colors"
            title="Nova conversa"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Nova
        </button>
    </div>

    <!-- Mensagens -->
    <div class="flex-1 px-5 py-5 overflow-y-auto space-y-4 min-h-0" id="bruce-chat-messages">
        @forelse($messages as $msg)
            @if($msg->role === 'user')
                <div class="flex justify-end">
                    <div class="max-w-[85%] bg-[#0A0A0B] text-white px-4 py-3 rounded-2xl rounded-br-md text-[15px] leading-relaxed shadow-sm">
                        {{ $msg->content }}
                    </div>
                </div>
            @elseif($msg->role === 'assistant' && filled($msg->content))
                {{-- So renderiza se tem conteudo real; mensagens intermediarias de tool_calls (content=null) sao invisiveis pro cliente --}}
                <div class="flex justify-start items-end gap-2">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="" class="w-8 h-8 shrink-0 mb-1">
                    <div class="max-w-[85%] bg-white border border-black/5 text-[#0A0A0B] px-4 py-3 rounded-2xl rounded-bl-md shadow-sm">
                        <div class="prose prose-slate max-w-none text-[15px] leading-relaxed
                                    prose-p:my-2 prose-p:leading-relaxed
                                    prose-headings:text-[#0A0A0B] prose-headings:font-display prose-headings:font-bold
                                    prose-strong:text-[#0A0A0B]
                                    prose-a:text-[#FF7A1A] prose-a:font-semibold
                                    prose-ul:my-2 prose-li:my-1
                                    prose-code:text-[#FF7A1A] prose-code:bg-[#F4F4F5] prose-code:px-1 prose-code:py-0.5 prose-code:rounded prose-code:before:content-none prose-code:after:content-none">
                            {!! Str::markdown($msg->content) !!}
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="flex flex-col items-center justify-center text-center h-full text-[#3A3A3C] py-8 px-4">
                <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="BruceIA" class="w-20 h-20 mb-5 opacity-90">
                <p class="font-display font-bold text-[#0A0A0B] text-xl">Oi! Sou o Bruce.</p>
                <p class="text-sm mt-3 leading-relaxed max-w-[280px]">
                    Pergunte sobre <b class="text-[#0A0A0B]">caixa do mês</b>, <b class="text-[#0A0A0B]">clientes sumidos</b> ou peça pra eu redigir mensagens.
                </p>
            </div>
        @endforelse

        <!-- Indicador digitando (comeca hidden; Livewire remove a classe durante o envio) -->
        <div wire:loading.class.remove="hidden" wire:target="sendMessage" class="hidden items-end gap-2">
            <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="" class="w-8 h-8 shrink-0 mb-1">
            <div class="bg-white border border-black/5 px-5 py-4 rounded-2xl rounded-bl-md shadow-sm">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-bounce" style="animation-delay: 0s"></span>
                    <span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-bounce" style="animation-delay: 0.15s"></span>
                    <span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-bounce" style="animation-delay: 0.3s"></span>
                </div>
            </div>
        </div>

        @error('limit')
            <div class="mx-auto max-w-[90%] bg-red-50 text-red-800 p-3 rounded-xl text-sm font-medium border border-red-100 text-center">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Input -->
    <div class="px-4 py-4 bg-white border-t border-black/5 shrink-0">
        <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
            <input
                type="text"
                wire:model.defer="message"
                placeholder="Pergunte ao Bruce..."
                class="flex-1 rounded-full border border-[#3A3A3C]/15 bg-[#F4F4F5] px-5 py-3 text-[15px] text-[#0A0A0B] placeholder:text-[#3A3A3C]/60 focus:border-[#FF7A1A] focus:ring-2 focus:ring-[#FF7A1A]/20 focus:outline-none transition"
                required
                autocomplete="off"
                wire:loading.attr="disabled"
                wire:target="sendMessage"
            >
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="sendMessage"
                class="shrink-0 w-12 h-12 rounded-full bg-[#0A0A0B] hover:bg-[#FF7A1A] text-white flex items-center justify-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="Enviar"
            >
                <svg wire:loading.remove wire:target="sendMessage" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19V5m-7 7l7-7 7 7"/>
                </svg>
                <svg wire:loading wire:target="sendMessage" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        </form>
        <p class="text-[11px] text-[#3A3A3C]/60 text-center mt-2.5 font-medium">
            BruceIA pode errar. Confira dados críticos.
        </p>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', () => {
        const scrollToBottom = () => {
            const container = document.getElementById('bruce-chat-messages');
            if (container) container.scrollTop = container.scrollHeight;
        };

        scrollToBottom();

        Livewire.hook('message.processed', () => {
            setTimeout(scrollToBottom, 50);
        });
    });
</script>
