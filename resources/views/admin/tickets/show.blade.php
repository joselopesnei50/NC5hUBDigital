<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.tickets.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                        {{ $ticket->assunto }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-[#8A8F9C]">Cliente: <strong>{{ $ticket->cliente->razao_social ?? 'Deletado' }}</strong></span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $ticket->status == 'aberto' ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700' }} uppercase">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
            </div>

            @if($ticket->status === 'aberto')
                <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-white border border-gray-300 text-[#0A1128] px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-colors shadow-sm">
                        Encerrar Chamado
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl flex flex-col shadow-sm" style="min-height: 60vh;">
        <!-- Thread Area -->
        <div class="flex-1 p-6 overflow-y-auto space-y-6" id="chat-container">
            @foreach($ticket->mensagens as $mensagem)
                @php
                    $isAdmin = $mensagem->user->role === 'admin';
                @endphp
                
                <div class="flex flex-col {{ $isAdmin ? 'items-end' : 'items-start' }}">
                    <div class="flex items-center gap-2 mb-1 {{ $isAdmin ? 'flex-row-reverse' : '' }}">
                        <span class="text-xs font-bold text-[#8A8F9C]">{{ $isAdmin ? 'Você (Suporte)' : ($ticket->cliente->razao_social ?? 'Cliente') }}</span>
                        <span class="text-[10px] text-gray-400">{{ $mensagem->created_at->format('d/m H:i') }}</span>
                    </div>
                    
                    <div class="max-w-[80%] md:max-w-[60%] rounded-2xl p-4 shadow-sm {{ $isAdmin ? 'bg-[#0A1128] text-white rounded-tr-none' : 'bg-[#F4F5F7] text-[#0A1128] border border-gray-100 rounded-tl-none' }}">
                        <p class="text-sm whitespace-pre-line">{{ $mensagem->mensagem }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Reply Area -->
        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            @if($ticket->status === 'fechado')
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-xl text-sm mb-4">
                    <strong>Atenção:</strong> Este chamado está encerrado. Responder irá reabri-lo automaticamente.
                </div>
            @endif
            
            <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST">
                @csrf
                <div class="relative">
                    <textarea name="mensagem" required rows="3" class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128] pr-24 resize-none shadow-sm" placeholder="Digite sua resposta para o cliente..."></textarea>
                    <button type="submit" class="absolute bottom-3 right-3 bg-[#E63888] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-opacity-90 transition-colors shadow-md">
                        Responder
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Scroll to bottom on load
        const chatContainer = document.getElementById('chat-container');
        if(chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    </script>
</x-admin-layout>
