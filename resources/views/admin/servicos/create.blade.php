<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Cadastrar Novo Serviço
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-3xl">
        <form action="{{ route('admin.servicos.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome do Pacote / Serviço</label>
                    <input type="text" name="nome" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Gestão de Redes Sociais Pro">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Descrição Curta</label>
                    <textarea name="descricao" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="O que está incluso neste pacote..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Preço Base (R$)</label>
                    <input type="number" step="0.01" name="preco" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="1500.00">
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.servicos.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Adicionar ao Catálogo
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
