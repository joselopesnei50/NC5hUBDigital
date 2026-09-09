<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-display font-extrabold text-2xl text-ink leading-tight">
                    Caixa de Saída da IA
                </h2>
                <p class="text-slateText text-sm mt-0.5">Revise as mensagens que o BruceIA sugeriu antes do envio aos seus clientes.</p>
            </div>
            <a href="{{ route('customer.index') }}" class="text-sm font-bold text-bruce hover:underline">&larr; Voltar ao Painel</a>
        </div>
    </x-slot>

    <div class="bg-white border border-black/5 rounded-3xl overflow-hidden shadow-premium">
        @if($drafts->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($drafts as $draft)
                    <div class="p-6 md:p-8 flex flex-col lg:flex-row gap-6 hover:bg-slate-50/50 transition">
                        <!-- Lado Esquerdo: Meta Data -->
                        <div class="lg:w-1/4 flex-shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                @if($draft->status->value === 'rascunho')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-widest bg-yellow-100 text-yellow-700">Aguardando Aprovação</span>
                                @elseif($draft->status->value === 'aprovado' || $draft->status->value === 'enviado')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-widest bg-emerald-100 text-emerald-700">Enviado</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-widest bg-slate-100 text-slate-500">Descartado</span>
                                @endif
                                
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-widest bg-indigo-50 text-indigo-600">
                                    {{ strtoupper($draft->channel) }}
                                </span>
                            </div>
                            
                            <p class="text-xs font-bold text-slate uppercase tracking-wider mb-1">Destinatário</p>
                            <p class="text-sm font-bold text-ink mb-4">
                                {{ $draft->clienteFinal ? $draft->clienteFinal->nome : 'Toda a base (Disparo em massa)' }}
                            </p>

                            <p class="text-xs font-bold text-slate uppercase tracking-wider mb-1">Gatilho da IA</p>
                            <p class="text-sm text-slate-600">{{ $draft->trigger }}</p>
                            
                            <p class="text-xs text-slate-400 mt-4">{{ $draft->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <!-- Lado Direito: Preview da Mensagem -->
                        <div class="lg:w-3/4 flex flex-col justify-between bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                            <div>
                                @if($draft->subject)
                                    <div class="mb-4 pb-4 border-b border-slate-100">
                                        <p class="text-xs font-bold text-slate uppercase tracking-wider mb-1">Assunto do E-mail</p>
                                        <p class="text-base font-bold text-ink">{{ $draft->subject }}</p>
                                    </div>
                                @endif
                                
                                <p class="text-xs font-bold text-slate uppercase tracking-wider mb-2">Conteúdo Gerado</p>
                                <div class="prose prose-sm prose-slate max-w-none text-slate-700">
                                    {!! nl2br(e($draft->body)) !!}
                                </div>
                            </div>
                            
                            @if($draft->status->value === 'rascunho')
                                <div class="mt-6 pt-4 border-t border-slate-100 flex gap-3 justify-end">
                                    <form action="{{ route('customer.agent-drafts.discard', $draft->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-rose-600 transition">
                                            Descartar
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('customer.agent-drafts.approve', $draft->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-[#0A1128] hover:bg-indigo-600 text-white px-6 py-2 rounded-xl text-sm font-bold transition shadow-sm">
                                            Aprovar e Enviar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($drafts->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50">
                    {{ $drafts->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-lg font-display font-bold text-ink">Nenhuma mensagem aguardando</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-md">O BruceIA ainda não sugeriu nenhuma mensagem de engajamento para seus clientes.</p>
                <a href="{{ route('customer.index') }}" class="mt-6 bg-bruce hover:bg-bruceDark text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-md">
                    Pedir no Chat
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
