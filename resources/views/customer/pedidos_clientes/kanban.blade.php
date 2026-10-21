<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Pipeline Visual · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">Quadro Kanban de Pedidos</h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                    <a href="{{ route('customer.pedidos.index') }}" class="px-4 py-2 text-xs font-bold rounded-lg text-gray-600 hover:text-[#0A1128] transition-all">
                        Lista
                    </a>
                    <a href="{{ route('customer.pedidos.kanban') }}" class="px-4 py-2 text-xs font-bold rounded-lg bg-[#0A1128] text-white transition-all">
                        Quadro Kanban
                    </a>
                </div>

                <a href="{{ route('customer.pedidos.create') }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Novo Pedido
                </a>
            </div>
        </div>
    </x-slot>

    <div class="flex overflow-x-auto pb-6 gap-5 items-start h-[calc(100vh-210px)] min-h-[520px]">
        @foreach(['Orçamento', 'Aguardando Pagamento', 'Em Andamento', 'Concluído'] as $coluna)
            @php
                $dotColor = match($coluna) {
                    'Orçamento'            => 'bg-slate-400',
                    'Aguardando Pagamento' => 'bg-amber-500',
                    'Em Andamento'         => 'bg-blue-500',
                    'Concluído'            => 'bg-emerald-500',
                };
            @endphp

            <div class="flex flex-col min-w-[300px] max-w-[300px] md:min-w-[320px] md:max-w-[320px] rounded-2xl bg-gray-100/80 border border-gray-200/80 h-full overflow-hidden">
                <div class="px-4 py-3.5 bg-white border-b border-gray-200/80 flex justify-between items-center">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                        <span class="font-bold text-sm text-[#0A1128]">{{ $coluna }}</span>
                    </div>
                    <span class="bg-gray-100 text-[#0A1128] text-xs font-extrabold px-2.5 py-0.5 rounded-full">
                        {{ $kanban[$coluna]->count() }}
                    </span>
                </div>

                <div class="p-3 flex-1 overflow-y-auto space-y-3 sortable-list" data-status="{{ $coluna }}">
                    @foreach($kanban[$coluna] as $pedido)
                        <div class="bg-white p-4 rounded-xl shadow-sm cursor-grab border border-gray-200/70 hover:border-[#FF7A1A]/50 hover:shadow-md transition-all relative group" data-id="{{ $pedido->id }}">
                            <div class="absolute top-3 right-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('customer.pedidos.edit', $pedido->id) }}" class="text-gray-500 hover:text-[#FF7A1A] bg-gray-50 hover:bg-orange-50 rounded-lg p-1.5 transition-colors" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            </div>
                            <p class="text-[10px] text-gray-400 font-bold mb-1 uppercase tracking-wider">Pedido #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</p>
                            <h4 class="font-bold text-[#0A1128] text-sm mb-1 leading-snug pr-6">{{ $pedido->titulo }}</h4>
                            <p class="text-xs text-gray-500 mb-3 truncate">{{ $pedido->clienteFinal->nome_empresa ?? '—' }}</p>

                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <span class="text-sm font-extrabold text-[#0A1128]">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                                <span class="text-[11px] font-medium text-gray-400">{{ $pedido->data_pedido ? $pedido->data_pedido->format('d/m') : $pedido->created_at->format('d/m') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const columns = document.querySelectorAll('.sortable-list');

            columns.forEach(col => {
                new Sortable(col, {
                    group: 'pedidos',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: function (evt) {
                        const itemEl = evt.item;
                        const newStatus = evt.to.getAttribute('data-status');
                        const pedidoId = itemEl.getAttribute('data-id');

                        if (evt.from !== evt.to) {
                            const countFrom = evt.from.previousElementSibling.querySelector('span:last-child');
                            const countTo = evt.to.previousElementSibling.querySelector('span:last-child');
                            countFrom.textContent = parseInt(countFrom.textContent) - 1;
                            countTo.textContent = parseInt(countTo.textContent) + 1;

                            fetch(`/area-cliente/pedidos/${pedidoId}/status`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ status: newStatus })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if(!data.success) {
                                    alert('Erro ao atualizar status do pedido.');
                                }
                            })
                            .catch(error => {
                                console.error('Erro:', error);
                                alert('Ocorreu um erro ao comunicar com o servidor.');
                            });
                        }
                    }
                });
            });
        });
    </script>
</x-app-layout>
