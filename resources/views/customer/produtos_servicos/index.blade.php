<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Produtos e Serviços') }}
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
                        <form method="GET" action="{{ route('customer.produtos.index') }}" class="w-full md:w-1/2 flex">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome ou descrição..." class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">Buscar</button>
                            @if($search)
                                <a href="{{ route('customer.produtos.index') }}" class="ml-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Limpar</a>
                            @endif
                        </form>
                        
                        <a href="{{ route('customer.produtos.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Novo Item
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200">
                                    <th class="p-3 font-semibold text-sm text-gray-700 w-24">Tipo</th>
                                    <th class="p-3 font-semibold text-sm text-gray-700">Nome</th>
                                    <th class="p-3 font-semibold text-sm text-gray-700">Preço Padrão</th>
                                    <th class="p-3 font-semibold text-sm text-gray-700 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produtos as $item)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="p-3">
                                            @if($item->tipo == 'Produto')
                                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full font-semibold">Produto</span>
                                            @else
                                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full font-semibold">Serviço</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <div class="font-bold text-gray-800">{{ $item->nome }}</div>
                                            @if($item->descricao)
                                                <div class="text-xs text-gray-500 truncate max-w-xs" title="{{ $item->descricao }}">{{ $item->descricao }}</div>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <div class="font-semibold text-green-700">R$ {{ number_format($item->preco_padrao, 2, ',', '.') }}</div>
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('customer.produtos.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                <form action="{{ route('customer.produtos.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este item do catálogo?');">
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
                                        <td colspan="4" class="p-6 text-center text-gray-500">
                                            Nenhum item encontrado no catálogo.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $produtos->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
