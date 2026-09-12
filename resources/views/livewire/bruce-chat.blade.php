<div class="flex flex-col h-full bg-[#F4F4F5]">
    <!-- Cabeçalho preto BruceIA -->
    <div class="relative bg-[#0A0A0B] px-5 py-4 flex items-center gap-3">
        <div class="shrink-0">
            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-10 h-10">
        </div>
        <div class="flex-1 min-w-0 pr-10">
            <h3 class="font-display font-extrabold text-white text-lg leading-none tracking-tight">
                <span class="text-white">Bruce</span><span class="text-[#FF7A1A]">IA</span>
            </h3>
            <p class="text-white/50 text-[11px] mt-1 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Online · Analista de negócios
            </p>
        </div>
    </div>

    <!-- Mensagens -->
    <div class="flex-1 px-4 py-4 overflow-y-auto space-y-3" id="bruce-chat-messages">
        @forelse($messages as $msg)
            @if($msg->role === 'user')
                <div class="flex justify-end">
                    <div class="max-w-[85%] bg-[#FF7A1A] text-white px-4 py-2.5 rounded-2xl rounded-br-md text-sm leading-relaxed shadow-sm">
                        {{ $msg->content }}
                    </div>
                </div>
            @elseif($msg->role === 'assistant')
                <div class="flex justify-start items-end gap-2">
                    <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="" class="w-7 h-7 shrink-0 mb-0.5">
                    <div class="max-w-[80%] bg-white border border-black/5 text-[#0A0A0B] px-4 py-2.5 rounded-2xl rounded-bl-md text-sm leading-relaxed shadow-sm">
                        <div class="prose prose-sm max-w-none prose-p:my-1 prose-headings:text-[#0A0A0B] prose-strong:text-[#0A0A0B] prose-a:text-[#FF7A1A]">
                            {!! Str::markdown($msg->content ?? '') !!}
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="flex flex-col items-center justify-center text-center h-full text-[#3A3A3C] py-8 px-4">
                <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="BruceIA" class="w-16 h-16 mb-4 opacity-90">
                <p class="font-display font-bold text-[#0A0A0B] text-base">Oi! Sou o Bruce.</p>
                <p class="text-xs mt-2 leading-relaxed max-w-[240px]">
                    Pergunte sobre <b class="text-[#0A0A0B]">caixa do mês</b>, <b class="text-[#0A0A0B]">clientes sumidos</b> ou peça pra eu redigir mensagens.
                </p>
            </div>
        @endforelse

        <!-- Indicador digitando -->
        <div wire:loading wire:target="sendMessage" class="flex items-end gap-2">
            <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="" class="w-7 h-7 shrink-0 mb-0.5">
            <div class="bg-white border border-black/5 px-4 py-3 rounded-2xl rounded-bl-md shadow-sm">
                <div class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF7A1A] animate-bounce" style="animation-delay: 0s"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF7A1A] animate-bounce" style="animation-delay: 0.15s"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF7A1A] animate-bounce" style="animation-delay: 0.3s"></span>
                </div>
            </div>
        </div>

        @error('limit')
            <div class="text-center bg-red-50 text-red-700 p-2.5 rounded-xl text-xs font-semibold border border-red-100">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Input -->
    <div class="px-4 py-3 bg-white border-t border-black/5">
        <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
            <input
                type="text"
                wire:model.defer="message"
                placeholder="Pergunte ao Bruce..."
                class="flex-1 rounded-full border border-[#3A3A3C]/15 bg-[#F4F4F5] px-4 py-2.5 text-sm text-[#0A0A0B] placeholder:text-[#3A3A3C]/60 focus:border-[#FF7A1A] focus:ring-2 focus:ring-[#FF7A1A]/20 focus:outline-none transition"
                required
                autocomplete="off"
                wire:loading.attr="disabled"
                wire:target="sendMessage"
            >
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="sendMessage"
                class="shrink-0 w-11 h-11 rounded-full bg-[#0A0A0B] hover:bg-[#FF7A1A] text-white flex items-center justify-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="Enviar"
            >
                <svg wire:loading.remove wire:target="sendMessage" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19V5m-7 7l7-7 7 7"/>
                </svg>
                <svg wire:loading wire:target="sendMessage" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        </form>
        <p class="text-[10px] text-[#3A3A3C]/60 text-center mt-2 font-medium">
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

        Livewire.hook('message.processed', (message, component) => {
            scrollToBottom();
        });
    });
</script>
