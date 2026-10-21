<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Relacionamento · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">Base de Clientes</h2>
            </div>
            <div>
                <a href="{{ route('customer.clientes-finais.create') }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Novo Cliente
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

        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            {{-- Barra de Busca --}}
            <div class="p-6 border-b border-gray-100">
                <form method="GET" action="{{ route('customer.clientes-finais.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome, responsável, e-mail ou telefone..."
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-[#0A1128] hover:bg-[#141D38] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors">
                            Buscar
                        </button>
                        @if($search)
                            <a href="{{ route('customer.clientes-finais.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabela --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                            <th class="px-6 py-4">Cliente / Empresa</th>
                            <th class="px-6 py-4">Responsável</th>
                            <th class="px-6 py-4">Contato</th>
                            <th class="px-6 py-4">Cidade</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($clientesFinais as $cliente)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-[#0A1128]">{{ $cliente->nome_empresa }}</div>
                                    @if($cliente->data_aniversario_empresa)
                                        <div class="text-xs text-gray-400 mt-0.5" title="Fundação da Empresa">🎂 {{ $cliente->data_aniversario_empresa->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">{{ $cliente->nome_responsavel ?? '—' }}</div>
                                    @if($cliente->data_aniversario_responsavel)
                                        <div class="text-xs text-gray-400 mt-0.5" title="Aniversário do Responsável">🎂 {{ $cliente->data_aniversario_responsavel->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 space-y-0.5">
                                    @if($cliente->telefone)
                                        <div class="text-xs text-gray-600"><span class="font-bold text-gray-800">Tel:</span> {{ $cliente->telefone }}</div>
                                    @endif
                                    @if($cliente->email)
                                        <div class="text-xs text-gray-600"><span class="font-bold text-gray-800">E-mail:</span> {{ $cliente->email }}</div>
                                    @endif
                                    @if(!$cliente->telefone && !$cliente->email)
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-medium">
                                    {{ $cliente->cidade ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <a href="{{ route('customer.clientes-finais.edit', $cliente->id) }}"
                                           class="p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('customer.clientes-finais.destroy', $cliente->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
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
                                <td colspan="5" class="px-6 py-14 text-center text-gray-400 font-medium">
                                    Nenhum cliente cadastrado na base até o momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($clientesFinais->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $clientesFinais->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
