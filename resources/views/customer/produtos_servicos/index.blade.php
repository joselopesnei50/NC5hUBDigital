<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Catálogo Comercial · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">Produtos e Serviços</h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('customer.produtos.create') }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Novo Item
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl text-sm">
                <ul class="list-disc list-inside space-y-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            {{-- Barra de Busca & Importação CSV --}}
            <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <form method="GET" action="{{ route('customer.produtos.index') }}" class="flex flex-1 gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome ou descrição..."
                           class="flex-1 rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                    <button type="submit" class="bg-[#0A1128] hover:bg-[#141D38] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('customer.produtos.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                            Limpar
                        </a>
                    @endif
                </form>

                <form action="{{ route('customer.produtos.importar') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                    @csrf
                    <input type="file" name="csv_file" accept=".csv" required
                           class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-[#0A1128] hover:file:bg-gray-200 cursor-pointer" />
                    <button type="submit" class="bg-gray-800 hover:bg-[#0A1128] text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Importar CSV
                    </button>
                </form>
            </div>

            {{-- Tabela --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                            <th class="px-6 py-4 w-28">Tipo</th>
                            <th class="px-6 py-4">Item</th>
                            <th class="px-6 py-4">Preço Padrão</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($produtos as $item)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    @if($item->tipo == 'Produto')
                                        <span class="px-3 py-1 bg-purple-50 text-purple-700 border border-purple-200 text-xs rounded-full font-bold">Produto</span>
                                    @else
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 text-xs rounded-full font-bold">Serviço</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-[#0A1128]">{{ $item->nome }}</div>
                                    @if($item->descricao)
                                        <div class="text-xs text-gray-500 truncate max-w-md mt-0.5" title="{{ $item->descricao }}">{{ $item->descricao }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-extrabold text-[#0A1128]">R$ {{ number_format($item->preco_padrao, 2, ',', '.') }}</span>
                                    @if($item->unidade_medida)
                                        <span class="text-gray-400 text-xs font-medium">/ {{ $item->unidade_medida }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <a href="{{ route('customer.produtos.edit', $item->id) }}"
                                           class="p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('customer.produtos.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este item do catálogo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Excluir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center text-gray-400 font-medium">
                                    Nenhum produto ou serviço cadastrado no catálogo até o momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($produtos->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $produtos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
