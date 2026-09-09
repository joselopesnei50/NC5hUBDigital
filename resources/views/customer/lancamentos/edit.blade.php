<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest mb-2">Financeiro</p>
            <h2 class="font-display font-bold text-3xl text-[#0A1128] leading-tight">Editar Lançamento</h2>
        </div>
    </x-slot>

    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl shadow-slate-200/50 max-w-3xl">
        <form action="{{ route('customer.lancamentos.update', $lancamento) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Tipo de Lançamento *</label>
                    <select name="tipo" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="receber" {{ $lancamento->tipo == 'receber' ? 'selected' : '' }}>Conta a Receber (Entrada)</option>
                        <option value="pagar" {{ $lancamento->tipo == 'pagar' ? 'selected' : '' }}>Conta a Pagar (Saída)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status *</label>
                    <select name="status" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="pendente" {{ $lancamento->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="pago" {{ $lancamento->status == 'pago' ? 'selected' : '' }}>Pago / Recebido</option>
                        <option value="cancelado" {{ $lancamento->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Descrição *</label>
                    <input type="text" name="descricao" value="{{ $lancamento->descricao }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Valor (R$) *</label>
                    <input type="number" step="0.01" name="valor" value="{{ $lancamento->valor }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Vencimento *</label>
                    <input type="date" name="data_vencimento" value="{{ $lancamento->data_vencimento->format('Y-m-d') }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente (Para receitas)</label>
                    <select name="cliente_final_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Nenhum</option>
                        @foreach($clientesFinais as $cliente)
                            <option value="{{ $cliente->id }}" {{ $lancamento->cliente_final_id == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome_empresa }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Fornecedor (Para despesas)</label>
                    <input type="text" name="fornecedor" value="{{ $lancamento->fornecedor }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Categoria</label>
                    <input type="text" name="categoria" value="{{ $lancamento->categoria }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-slate-100">
                <a href="{{ route('customer.lancamentos.index', ['tipo' => $lancamento->tipo]) }}" class="px-6 py-3 font-bold text-slate-500 hover:text-[#0A1128] transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-[#0A1128]/20 hover:shadow-[#FF7A1A]/30">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
