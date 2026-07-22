<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

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
            <div class="bg-white border border-gray-200 rounded-3xl p-6 flex flex-col shadow-sm hover:shadow-md transition-shadow relative overflow-hidden"
                 x-data="{
                    modalAssinatura: false,
                    padReady: false,
                    initPad() {
                        if (this.padReady) return;
                        this.$nextTick(() => {
                            const canvas = document.getElementById('sig-{{ $contrato->id }}');
                            if (!canvas) return;
                            const ratio = Math.max(window.devicePixelRatio || 1, 1);
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = canvas.offsetHeight * ratio;
                            canvas.getContext('2d').scale(ratio, ratio);
                            window['pad_{{ $contrato->id }}'] = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)' });
                            this.padReady = true;
                        });
                    },
                    clearPad() {
                        const p = window['pad_{{ $contrato->id }}'];
                        if (p) p.clear();
                    }
                 }">

                {{-- Status badge --}}
                @if($contrato->status_assinatura === 'assinado')
                    <div class="absolute top-0 right-0 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">Assinado</div>
                @else
                    <div class="absolute top-0 right-0 bg-[#E63888] text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">Pendente</div>
                @endif

                <div class="w-12 h-12 bg-[#F4F5F7] rounded-xl flex items-center justify-center mb-4 text-[#0A1128]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>

                <h3 class="text-xl font-bold text-[#0A1128] mb-1 leading-tight">{{ $contrato->servico->nome ?? 'Contrato Avulso' }}</h3>
                <p class="text-[#8A8F9C] text-sm mb-6 flex-grow">Início em {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}</p>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div>
                        @if($contrato->servico)
                            <p class="text-xs text-[#8A8F9C] font-semibold uppercase tracking-wider">Valor Mensal</p>
                            <p class="text-lg font-bold text-[#0A1128]">R$ {{ number_format($contrato->servico->preco, 2, ',', '.') }}</p>
                        @else
                            <p class="text-xs text-[#8A8F9C] font-semibold uppercase tracking-wider">Contrato</p>
                            <p class="text-sm font-bold text-[#0A1128]">#{{ $contrato->id }}</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Download PDF --}}
                        <a href="{{ route('customer.contracts.pdf', $contrato->id) }}"
                           class="text-[#8A8F9C] hover:text-emerald-600 p-2 rounded-xl transition-colors" title="Baixar PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>

                        @if($contrato->status_assinatura !== 'assinado')
                            <button @click="modalAssinatura = true; initPad()"
                                    class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-4 py-2 rounded-xl text-sm font-bold transition-colors shadow-lg">
                                Assinar
                            </button>
                        @else
                            <div class="text-emerald-600 flex items-center gap-1 font-bold text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Ativo
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Modal de Assinatura --}}
                <div x-show="modalAssinatura" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 py-8">
                        <div x-show="modalAssinatura" x-transition.opacity class="fixed inset-0 bg-[#0A1128] bg-opacity-75" @click="modalAssinatura = false"></div>

                        <div x-show="modalAssinatura" x-transition
                             class="relative bg-white rounded-3xl shadow-xl w-full max-w-lg p-8 z-10 max-h-screen overflow-y-auto">

                            <div class="w-12 h-12 rounded-full bg-[#E63888]/10 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-[#E63888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </div>

                            <h3 class="text-2xl font-extrabold text-[#0A1128] mb-1">Assinatura Eletrônica</h3>
                            <p class="text-sm text-[#8A8F9C] mb-5">Contrato: <strong>{{ $contrato->servico->nome ?? 'Contrato #'.$contrato->id }}</strong></p>

                            @if($contrato->conteudo)
                                <div class="bg-[#F4F5F7] rounded-xl border border-gray-200 mb-5 overflow-hidden">
                                    <div class="px-4 py-2 border-b border-gray-200 bg-white">
                                        <p class="text-xs font-bold text-[#0A1128] uppercase tracking-wider">Leia antes de assinar</p>
                                    </div>
                                    <div class="p-4 max-h-48 overflow-y-auto text-xs text-[#0A1128] leading-relaxed ql-editor" style="padding:1rem;">
                                        {!! $contrato->conteudo !!}
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('customer.contracts.sign', $contrato->id) }}" method="POST" id="form-{{ $contrato->id }}">
                                @csrf

                                {{-- Pad de assinatura --}}
                                <div class="mb-5">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs font-bold text-[#0A1128] uppercase tracking-wider">Sua assinatura (opcional)</p>
                                        <button type="button" @click="clearPad()" class="text-xs text-[#8A8F9C] hover:text-red-500 font-medium">Limpar</button>
                                    </div>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 overflow-hidden" style="height:140px;">
                                        <canvas id="sig-{{ $contrato->id }}" style="width:100%;height:140px;touch-action:none;display:block;"></canvas>
                                    </div>
                                    <p class="text-[10px] text-[#8A8F9C] mt-1">Use o dedo (celular) ou o mouse para assinar acima.</p>
                                    <input type="hidden" name="signature_image" id="sig-data-{{ $contrato->id }}">
                                </div>

                                <div class="bg-[#F4F5F7] p-4 rounded-xl border border-gray-200 mb-5">
                                    <p class="font-bold text-[#0A1128] text-sm mb-1">Declaração de Aceite</p>
                                    <p class="italic text-xs text-[#8A8F9C]">"Declaro que li e concordo com os termos propostos para a prestação do serviço. Reconheço esta assinatura digital como válida."</p>
                                </div>

                                <p class="text-[11px] text-[#8A8F9C] mb-5">Seu IP ({{ request()->ip() }}) e horário serão registrados no aceite.</p>

                                <div class="flex gap-3">
                                    <button type="button" @click="modalAssinatura = false"
                                            class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-[#0A1128] font-bold py-3 rounded-xl transition-colors">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            @click="const p=window['pad_{{ $contrato->id }}']; if(p&&!p.isEmpty()){document.getElementById('sig-data-{{ $contrato->id }}').value=p.toDataURL();}"
                                            class="flex-1 bg-[#0A1128] hover:bg-[#FF7A1A] text-white font-bold py-3 rounded-xl transition-colors shadow-lg">
                                        Assinar Contrato
                                    </button>
                                </div>
                            </form>
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
