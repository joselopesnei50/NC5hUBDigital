<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Pedidos (Kanban)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex gap-2">
                    <a href="{{ route('customer.pedidos.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-l-md hover:bg-gray-300 font-medium border border-gray-300 border-r-0">Visualização em Lista</a>
                    <a href="{{ route('customer.pedidos.kanban') }}" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 font-bold border border-blue-600">Quadro Kanban</a>
                </div>
                <a href="{{ route('customer.pedidos.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center shadow">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Novo Pedido
                </a>
            </div>

            <div class="flex overflow-x-auto pb-8 gap-6 items-start h-[75vh]">
                
                @foreach(['Orçamento', 'Aguardando Pagamento', 'Em Andamento', 'Concluído'] as $coluna)
                    @php
                        $colorClass = match($coluna) {
                            'Orçamento' => 'border-gray-300 bg-gray-50',
                            'Aguardando Pagamento' => 'border-yellow-300 bg-yellow-50',
                            'Em Andamento' => 'border-blue-300 bg-blue-50',
                            'Concluído' => 'border-green-300 bg-green-50',
                        };
                        $headerClass = match($coluna) {
                            'Orçamento' => 'bg-gray-200 text-gray-700',
                            'Aguardando Pagamento' => 'bg-yellow-200 text-yellow-800',
                            'Em Andamento' => 'bg-blue-200 text-blue-800',
                            'Concluído' => 'bg-green-200 text-green-800',
                        };
                    @endphp

                    <div class="flex flex-col min-w-[300px] max-w-[300px] rounded-lg border-2 {{ $colorClass }} h-full">
                        <div class="px-4 py-3 font-bold rounded-t-md {{ $headerClass }} flex justify-between items-center shadow-sm">
                            <span>{{ $coluna }}</span>
                            <span class="bg-white/50 text-sm px-2 py-0.5 rounded-full">{{ $kanban[$coluna]->count() }}</span>
                        </div>
                        
                        <div class="p-2 flex-1 overflow-y-auto sortable-list" data-status="{{ $coluna }}">
                            @foreach($kanban[$coluna] as $pedido)
                                <div class="bg-white p-4 rounded shadow-sm mb-3 cursor-grab border border-gray-200 hover:shadow-md transition relative group" data-id="{{ $pedido->id }}">
                                    <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                        <a href="{{ route('customer.pedidos.edit', $pedido->id) }}" class="text-blue-500 hover:text-blue-700 bg-white rounded p-1 shadow" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    </div>
                                    <p class="text-[10px] text-gray-500 font-semibold mb-1 uppercase">Pedido #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <h4 class="font-bold text-gray-800 text-sm mb-1 leading-tight">{{ $pedido->titulo }}</h4>
                                    <p class="text-xs text-gray-600 mb-2 truncate">{{ $pedido->clienteFinal->nome_empresa }}</p>
                                    
                                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                                        <span class="text-sm font-bold text-green-600">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $pedido->data_pedido ? $pedido->data_pedido->format('d/m') : $pedido->created_at->format('d/m') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- CDN do SortableJS para Drag and Drop -->
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
                            // Atualizar contador de items das colunas
                            const countFrom = evt.from.previousElementSibling.querySelector('span:last-child');
                            const countTo = evt.to.previousElementSibling.querySelector('span:last-child');
                            countFrom.textContent = parseInt(countFrom.textContent) - 1;
                            countTo.textContent = parseInt(countTo.textContent) + 1;

                            // Fazer chamada AJAX (Fetch) para atualizar o banco
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
