<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Meus Briefings
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse($briefings as $briefing)
            <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm" x-data="{ modalResponder: false }">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-[#0A1128]">{{ $briefing->titulo }}</h3>
                        <p class="text-sm text-[#8A8F9C]">Enviado em {{ $briefing->created_at->format('d/m/Y') }} pela agência</p>
                    </div>
                    @if($briefing->status === 'pendente')
                        <span class="bg-[#E63888] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Pendente de Resposta
                        </span>
                    @else
                        <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Respondido
                        </span>
                    @endif
                </div>

                <div class="bg-[#F4F5F7] p-5 rounded-xl border border-gray-100 mb-6">
                    <p class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider mb-2">Instruções / Perguntas</p>
                    <p class="text-sm text-[#0A1128] whitespace-pre-line">{{ $briefing->descricao }}</p>

                    @if($briefing->anexo_admin_path)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ Storage::url($briefing->anexo_admin_path) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-[#E63888] hover:text-[#0A1128] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Baixar Anexo da Agência
                            </a>
                        </div>
                    @endif
                </div>

                @if($briefing->status === 'pendente')
                    <button @click="modalResponder = true" class="w-full md:w-auto bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-8 py-3 rounded-xl text-sm font-bold transition-colors shadow-lg">
                        Responder Briefing
                    </button>
                @else
                    <div class="bg-emerald-50 border border-emerald-100 p-5 rounded-xl">
                        <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-2">Sua Resposta</p>
                        <p class="text-sm text-[#0A1128] whitespace-pre-line">{{ $briefing->resposta_cliente }}</p>
                        @if($briefing->anexo_cliente_path)
                            <div class="mt-4 pt-4 border-t border-emerald-200/50">
                                <a href="{{ Storage::url($briefing->anexo_cliente_path) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-900 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    Ver seu anexo enviado
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Modal de Resposta -->
                <div x-show="modalResponder" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="modalResponder" x-transition class="fixed inset-0 bg-[#0A1128] bg-opacity-75 transition-opacity" @click="modalResponder = false"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div x-show="modalResponder" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                            <form action="{{ route('customer.briefings.answer', $briefing->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
                                @csrf
                                <h3 class="text-2xl font-bold text-[#0A1128] mb-6">Responder Briefing: {{ $briefing->titulo }}</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-[#0A1128] mb-1">Sua Resposta</label>
                                        <textarea name="resposta_cliente" rows="8" required class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128] shadow-sm" placeholder="Escreva aqui todas as informações necessárias..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-[#0A1128] mb-1">Anexar Arquivo (Opcional)</label>
                                        <p class="text-xs text-[#8A8F9C] mb-2">Envie planilhas, referências de imagem, logos, etc.</p>
                                        <input type="file" name="anexo_cliente" class="w-full text-sm text-[#8A8F9C] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#F4F5F7] file:text-[#0A1128] hover:file:bg-gray-200">
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end gap-3">
                                    <button type="button" @click="modalResponder = false" class="px-6 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-bold text-[#0A1128] hover:bg-gray-50">Cancelar</button>
                                    <button type="submit" class="px-6 py-2.5 bg-[#0A1128] text-white rounded-xl text-sm font-bold hover:bg-[#FF7A1A] transition-colors shadow-lg">Enviar Resposta</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-3xl p-12 text-center shadow-sm">
                <svg class="w-16 h-16 text-[#8A8F9C] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <h3 class="text-xl font-bold text-[#0A1128] mb-2">Nenhum briefing pendente</h3>
                <p class="text-[#8A8F9C]">Quando a agência precisar de informações estruturadas, os formulários aparecerão aqui.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
