<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest mb-2">Operacional</p>
            <h2 class="font-display font-bold text-3xl text-[#0A1128] leading-tight">{{ isset($projeto) ? 'Editar Projeto' : 'Solicitar Projeto' }}</h2>
        </div>
    </x-slot>

    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl shadow-slate-200/50 max-w-3xl">
        <form action="{{ isset($projeto) ? route('customer.projetos.update', $projeto) : route('customer.projetos.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($projeto)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome do Projeto *</label>
                    <input type="text" name="nome" value="{{ $projeto->nome ?? '' }}" required placeholder="Ex: Criação de Website" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente *</label>
                    <select name="cliente_final_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">(Nenhum - Projeto Interno)</option>
                        @foreach($clientesFinais as $cliente)
                            <option value="{{ $cliente->id }}" {{ (isset($projeto) && $projeto->cliente_final_id == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome_empresa }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status Inicial *</label>
                    <select name="status" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="pendente" {{ (isset($projeto) && $projeto->status == 'pendente') ? 'selected' : '' }}>Pendente</option>
                        <option value="em_andamento" {{ (isset($projeto) && $projeto->status == 'em_andamento') ? 'selected' : '' }}>Em Andamento</option>
                        <option value="aguardando_cliente" {{ (isset($projeto) && $projeto->status == 'aguardando_cliente') ? 'selected' : '' }}>Aguardando Você (Cliente)</option>
                        <option value="concluido" {{ (isset($projeto) && $projeto->status == 'concluido') ? 'selected' : '' }}>Concluído</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Descrição / Escopo</label>
                    <textarea name="descricao" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">{{ $projeto->descricao ?? '' }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-slate-100">
                <a href="{{ route('customer.projetos.index') }}" class="px-6 py-3 font-bold text-slate-500 hover:text-[#0A1128] transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-[#0A1128]/20 hover:shadow-[#FF7A1A]/30">
                    {{ isset($projeto) ? 'Salvar Alterações' : 'Criar Projeto' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
