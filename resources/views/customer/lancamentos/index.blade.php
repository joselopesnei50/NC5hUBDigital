<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest mb-2">Financeiro</p>
                <h2 class="font-display font-bold text-3xl text-[#0A1128] leading-tight">Gestão Financeira</h2>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('customer.lancamentos.index', ['tipo' => 'receber']) }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ $tipo === 'receber' ? 'bg-[#0A1128] text-white' : 'bg-white text-gray-700 border hover:bg-gray-50' }}">
                    Contas a Receber
                </a>
                <a href="{{ route('customer.lancamentos.index', ['tipo' => 'pagar']) }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ $tipo === 'pagar' ? 'bg-[#0A1128] text-white' : 'bg-white text-gray-700 border hover:bg-gray-50' }}">
                    Contas a Pagar
                </a>
                <a href="{{ route('customer.lancamentos.create', ['tipo' => $tipo]) }}" class="px-4 py-2 bg-[#FF7A1A] text-white text-sm font-bold rounded-lg hover:bg-orange-600 transition shadow-lg">
                    + Novo Lançamento
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-3">Descrição</th>
                            <th class="pb-3">Categoria</th>
                            <th class="pb-3">Vencimento</th>
                            <th class="pb-3">Valor</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($lancamentos as $lancamento)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4">
                                    <p class="font-bold text-[#0A1128]">{{ $lancamento->descricao }}</p>
                                    @if($lancamento->fornecedor)
                                        <p class="text-xs text-slate-500">Fornecedor: {{ $lancamento->fornecedor }}</p>
                                    @endif
                                    @if($lancamento->clienteFinal)
                                        <p class="text-xs text-slate-500">Cliente: {{ $lancamento->clienteFinal->nome_empresa }}</p>
                                    @endif
                                </td>
                                <td class="py-4 text-sm text-slate-600">{{ $lancamento->categoria ?? '-' }}</td>
                                <td class="py-4 text-sm text-slate-600">
                                    {{ $lancamento->data_vencimento->format('d/m/Y') }}
                                </td>
                                <td class="py-4 font-bold text-[#0A1128]">
                                    R$ {{ number_format($lancamento->valor, 2, ',', '.') }}
                                </td>
                                <td class="py-4">
                                    @if($lancamento->status === 'pago')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Pago</span>
                                    @elseif($lancamento->status === 'pendente')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Pendente</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Cancelado</span>
                                    @endif
                                </td>
                                <td class="py-4 text-right flex justify-end gap-2">
                                    <a href="{{ route('customer.lancamentos.edit', $lancamento) }}" class="p-2 text-slate-400 hover:text-[#0A1128] transition-colors rounded-lg hover:bg-slate-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('customer.lancamentos.destroy', $lancamento) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    Nenhum lançamento encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
