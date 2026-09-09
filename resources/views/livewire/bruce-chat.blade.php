<div class="flex flex-col h-[600px] bg-white rounded-2xl shadow-sm border border-slate-200">
    <!-- Cabeçalho -->
    <div class="p-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#0A1128] flex items-center justify-center text-white font-bold shadow-md">
                B
            </div>
            <div>
                <h3 class="font-bold text-[#0A1128]">BruceIA</h3>
                <p class="text-xs text-slate-500">Assistente Analítico Oficial</p>
            </div>
        </div>
    </div>

    <!-- Corpo das Mensagens -->
    <div class="flex-1 p-4 overflow-y-auto space-y-4" id="chat-messages">
        @forelse($messages as $msg)
            @if($msg->role === 'user')
                <!-- Balão do Cliente -->
                <div class="flex justify-end">
                    <div class="max-w-[80%] bg-[#0A1128] text-white p-3 rounded-2xl rounded-tr-none text-sm shadow-sm">
                        {{ $msg->content }}
                    </div>
                </div>
            @elseif($msg->role === 'assistant')
                <!-- Balão do Bruce -->
                <div class="flex justify-start">
                    <div class="max-w-[80%] bg-slate-100 text-slate-800 p-4 rounded-2xl rounded-tl-none text-sm shadow-sm">
                        <!-- Permite tags embutidas do Laravel para renderizar Negritos e Listas que a IA gera (Markdown) -->
                        <div class="prose prose-sm prose-slate max-w-none">
                            {!! Str::markdown($msg->content ?? '') !!}
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="flex h-full items-center justify-center text-center text-slate-400">
                <p>Olá! Sou o Bruce.<br>Pergunte-me como foram as vendas ou peça para eu redigir mensagens para clientes!</p>
            </div>
        @endforelse

        <!-- Indicador de Carregamento / "Digitando..." (Gatilho automático do Livewire) -->
        <div wire:loading wire:target="sendMessage" class="flex justify-start w-full">
            <div class="max-w-[80%] bg-slate-50 border border-slate-100 text-slate-600 p-3 rounded-2xl rounded-tl-none text-sm shadow-sm flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="animate-pulse font-medium">Bruce está consultando ferramentas...</span>
            </div>
        </div>
        
        <!-- Mensagem de erro financeira -->
        @error('limit')
            <div class="text-center bg-red-50 text-red-600 p-2 rounded-lg text-xs font-bold mt-2 border border-red-200">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Rodapé / Input -->
    <div class="p-4 border-t border-slate-200 bg-white rounded-b-2xl">
        <form wire:submit.prevent="sendMessage" class="flex gap-2">
            <input 
                type="text" 
                wire:model.defer="message" 
                placeholder="Pergunte ao Bruce..." 
                class="flex-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm" 
                required 
                autocomplete="off"
                wire:loading.attr="disabled"
                wire:target="sendMessage"
            >
            <button 
                type="submit" 
                wire:loading.attr="disabled" 
                wire:target="sendMessage"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl font-bold transition flex items-center shadow-sm disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="sendMessage">Enviar</span>
                <span wire:loading wire:target="sendMessage">Aguarde...</span>
            </button>
        </form>
    </div>
</div>

<script>
    // Script nativo do Livewire V3 para fazer o Scroll da conversa sempre descer para a mensagem mais recente
    document.addEventListener('livewire:initialized', () => {
        const scrollToBottom = () => {
            const container = document.getElementById('chat-messages');
            container.scrollTop = container.scrollHeight;
        };

        // Rola para baixo ao carregar
        scrollToBottom();

        // Rola para baixo sempre que o componente for desenhado na tela de novo
        Livewire.hook('morph.updated', (el, component) => {
            scrollToBottom();
        });
    });
</script>
