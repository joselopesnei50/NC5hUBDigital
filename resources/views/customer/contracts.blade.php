<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Meus Contratos e Serviços
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($contratos as $contrato)
            <div class="bg-white border border-gray-200 rounded-3xl p-6 flex flex-col shadow-sm hover:shadow-md transition-shadow relative overflow-hidden" x-data="{ modalAssinatura: false }">
                
                <!-- Tag de Status Visual -->
                @if($contrato->status_assinatura === 'assinado')
                    <div class="absolute top-0 right-0 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                        Assinado
                    </div>
                @else
                    <div class="absolute top-0 right-0 bg-[#E63888] text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                        Pendente de Assinatura
                    </div>
                @endif

                <!-- Ícone do Serviço -->
                <div class="w-12 h-12 bg-[#F4F5F7] rounded-xl flex items-center justify-center mb-4 text-[#0A1128]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>

                <h3 class="text-xl font-bold text-[#0A1128] mb-1 leading-tight">{{ $contrato->servico->nome ?? 'Serviço Personalizado' }}</h3>
                <p class="text-[#8A8F9C] text-sm mb-6 flex-grow">Início em {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}</p>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-[#8A8F9C] font-semibold uppercase tracking-wider">Valor Mensal</p>
                        <p class="text-lg font-bold text-[#0A1128]">R$ {{ number_format($contrato->servico->preco ?? 0, 2, ',', '.') }}</p>
                    </div>

                    @if($contrato->status_assinatura !== 'assinado')
                        <button @click="modalAssinatura = true" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-4 py-2 rounded-xl text-sm font-bold transition-colors shadow-lg">
                            Assinar
                        </button>
                    @else
                        <div class="text-emerald-600 flex items-center gap-1 font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Ativo
                        </div>
                    @endif
                </div>

                <!-- Modal de Assinatura -->
                <div x-show="modalAssinatura" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="modalAssinatura" x-transition.opacity class="fixed inset-0 bg-[#0A1128] bg-opacity-75 transition-opacity" aria-hidden="true" @click="modalAssinatura = false"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div x-show="modalAssinatura" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-8">
                            
                            <div class="w-12 h-12 rounded-full bg-[#E63888]/10 flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-[#E63888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </div>

                            <h3 class="text-2xl leading-6 font-extrabold text-[#0A1128] mb-4" id="modal-title">Assinatura Eletrônica</h3>
                            
                            <div class="mt-2 text-sm text-[#8A8F9C] space-y-4">
                                <p>Você está prestes a assinar o contrato referente ao serviço: <strong>{{ $contrato->servico->nome ?? 'Serviço' }}</strong>.</p>
                                <div class="bg-[#F4F5F7] p-4 rounded-xl border border-gray-200">
                                    <p class="font-medium text-[#0A1128] mb-1">Declaração de Aceite:</p>
                                    <p class="italic text-xs">"Declaro que li e concordo com os termos propostos para a prestação do serviço. Reconheço esta assinatura digital como válida."</p>
                                </div>
                                <p class="text-[11px]">Por segurança, seu endereço IP ({{ request()->ip() }}) e o horário atual serão registrados no aceite.</p>
                            </div>

                            <div class="mt-8 flex gap-3">
                                <button type="button" @click="modalAssinatura = false" class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-[#0A1128] font-bold py-3 px-4 rounded-xl transition-colors">
                                    Cancelar
                                </button>
                                
                                <form action="{{ route('customer.contracts.sign', $contrato->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-[#0A1128] hover:bg-[#FF7A1A] text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-lg">
                                        Assinar Contrato
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white border border-gray-200 rounded-3xl p-12 text-center shadow-sm">
                    <svg class="w-16 h-16 text-[#8A8F9C] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h3 class="text-xl font-bold text-[#0A1128] mb-2">Nenhum contrato ativo</h3>
                    <p class="text-[#8A8F9C]">Quando novos serviços forem contratados, os documentos aparecerão aqui para assinatura.</p>
                </div>
            </div>
        @endforelse
    </div>
</x-app-layout>
