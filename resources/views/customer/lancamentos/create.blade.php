<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest mb-2">Financeiro</p>
            <h2 class="font-display font-bold text-3xl text-[#0A1128] leading-tight">Novo Lançamento</h2>
        </div>
    </x-slot>

    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl shadow-slate-200/50 max-w-3xl">
        <form action="{{ route('customer.lancamentos.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Tipo de Lançamento *</label>
                    <select name="tipo" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="receber" {{ request('tipo') == 'receber' ? 'selected' : '' }}>Conta a Receber (Entrada)</option>
                        <option value="pagar" {{ request('tipo') == 'pagar' ? 'selected' : '' }}>Conta a Pagar (Saída)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status *</label>
                    <select name="status" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="pendente">Pendente</option>
                        <option value="pago">Pago / Recebido</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Descrição *</label>
                    <input type="text" name="descricao" required placeholder="Ex: Pagamento de Mensalidade" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Valor (R$) *</label>
                    <input type="number" step="0.01" name="valor" required placeholder="0.00" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Vencimento *</label>
                    <input type="date" name="data_vencimento" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente (Para receitas)</label>
                    <select name="cliente_final_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Nenhum</option>
                        @foreach($clientesFinais as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nome_empresa }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Fornecedor (Para despesas)</label>
                    <input type="text" name="fornecedor" placeholder="Ex: AWS, Google" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Categoria</label>
                    <input type="text" name="categoria" placeholder="Ex: Software, Impostos, Serviços" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-slate-100">
                <a href="{{ route('customer.lancamentos.index', ['tipo' => request('tipo', 'receber')]) }}" class="px-6 py-3 font-bold text-slate-500 hover:text-[#0A1128] transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-[#0A1128]/20 hover:shadow-[#FF7A1A]/30">
                    Salvar Lançamento
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
