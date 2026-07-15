<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.faturas.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Editar Fatura #{{ $fatura->id }}</h2>
        </div>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-3xl">
        <form action="{{ route('admin.faturas.update', $fatura->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente</label>
                    <select name="cliente_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}" @selected($fatura->cliente_id == $c->id)>{{ $c->razao_social }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Descrição</label>
                    <input type="text" name="descricao" value="{{ old('descricao', $fatura->descricao) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Valor (R$)</label>
                    <input type="number" step="0.01" name="valor" value="{{ old('valor', $fatura->valor) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Vencimento</label>
                    <input type="date" name="vencimento" value="{{ old('vencimento', optional($fatura->vencimento)->format('Y-m-d') ?? $fatura->vencimento) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                        @foreach(['pendente','pago','atrasado','cancelado'] as $st)
                            <option value="{{ $st }}" @selected($fatura->status == $st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.faturas.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">Salvar</button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100">
            <form action="{{ route('admin.faturas.destroy', $fatura->id) }}" method="POST" onsubmit="return confirm('Remover fatura?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm">Excluir fatura</button>
            </form>
        </div>
    </div>
</x-admin-layout>
