<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Pedidos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <form method="GET" action="{{ route('customer.pedidos.index') }}" class="w-full md:w-1/2 flex">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por título, descrição ou cliente..." class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">Buscar</button>
                            @if($search)
                                <a href="{{ route('customer.pedidos.index') }}" class="ml-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Limpar</a>
                            @endif
                        </form>
                        
                        <a href="{{ route('customer.pedidos.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Novo Pedido
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200">
                                    <th class="p-3 font-semibold text-sm text-gray-700">Pedido</th>
                                    <th class="p-3 font-semibold text-sm text-gray-700">Cliente</th>
                                    <th class="p-3 font-semibold text-sm text-gray-700">Valor</th>
                                    <th class="p-3 font-semibold text-sm text-gray-700">Status</th>
                                    <th class="p-3 font-semibold text-sm text-gray-700">Data Prev.</th>
                                    <th class="p-3 font-semibold text-sm text-gray-700 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pedidos as $pedido)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="p-3">
                                            <div class="font-bold text-gray-800">{{ $pedido->titulo }}</div>
                                            @if($pedido->data_pedido)
                                                <div class="text-xs text-gray-500">Feito em: {{ $pedido->data_pedido->format('d/m/Y') }}</div>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <div class="text-gray-800 font-medium">{{ $pedido->clienteFinal->nome_empresa }}</div>
                                            <div class="text-xs text-gray-500">{{ $pedido->clienteFinal->nome_responsavel }}</div>
                                        </td>
                                        <td class="p-3">
                                            <div class="font-semibold text-green-700">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</div>
                                        </td>
                                        <td class="p-3 text-gray-700">
                                            @php
                                                $badgeClass = match($pedido->status) {
                                                    'Orçamento' => 'bg-gray-100 text-gray-800',
                                                    'Aguardando Pagamento' => 'bg-yellow-100 text-yellow-800',
                                                    'Em Andamento' => 'bg-blue-100 text-blue-800',
                                                    'Concluído' => 'bg-green-100 text-green-800',
                                                    'Cancelado' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                                {{ $pedido->status }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-sm text-gray-700">
                                            {{ $pedido->data_entrega ? $pedido->data_entrega->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="flex justify-center gap-2 items-center">
                                                <button type="button" onclick="copiarLink('{{ route('public.pedido.show', $pedido->token_publico) }}')" class="text-gray-500 hover:text-gray-800" title="Copiar Link Público">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                </button>
                                                <a href="{{ route('customer.pedidos.edit', $pedido->id) }}" class="text-blue-600 hover:text-blue-800" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                <form action="{{ route('customer.pedidos.destroy', $pedido->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este pedido?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Excluir">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-6 text-center text-gray-500">
                                            Nenhum pedido encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pedidos->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function copiarLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                alert('Link público copiado para a área de transferência!');
            }).catch(err => {
                console.error('Erro ao copiar: ', err);
            });
        }
    </script>
</x-app-layout>
