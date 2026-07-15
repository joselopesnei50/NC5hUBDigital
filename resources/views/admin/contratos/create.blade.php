<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Emitir Novo Contrato
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-3xl">
        <form action="{{ route('admin.contratos.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente / Contratante</label>
                    <select name="cliente_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Selecione o Cliente...</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->razao_social }} ({{ $cliente->cpf_cnpj }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Serviço a Contratar</label>
                    <select name="servico_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Selecione o Serviço do Catálogo...</option>
                        @foreach($servicos as $servico)
                            <option value="{{ $servico->id }}">{{ $servico->nome }} — R$ {{ number_format($servico->preco, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Início</label>
                    <input type="date" name="data_inicio" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Término (Opcional)</label>
                    <input type="date" name="data_fim" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.contratos.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Emitir Contrato
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
