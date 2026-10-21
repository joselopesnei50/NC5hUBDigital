<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Catálogo · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">
                    Editar Item: {{ $produto->nome }}
                </h2>
            </div>
            <a href="{{ route('customer.produtos.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar ao Catálogo
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
            <div class="mb-6 pb-5 border-b border-gray-100">
                <h3 class="font-display font-extrabold text-lg text-[#0A1128]">Atualizar Dados do Item</h3>
                <p class="text-sm text-gray-500 mt-0.5">Altere valores, unidade ou especificações técnicas deste produto ou serviço.</p>
            </div>

            <form action="{{ route('customer.produtos.update', $produto->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Tipo --}}
                    <div>
                        <label for="tipo" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tipo *</label>
                        <select name="tipo" id="tipo" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            <option value="Serviço" {{ old('tipo', $produto->tipo) == 'Serviço' ? 'selected' : '' }}>Serviço</option>
                            <option value="Produto" {{ old('tipo', $produto->tipo) == 'Produto' ? 'selected' : '' }}>Produto</option>
                        </select>
                        @error('tipo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Preço Padrão --}}
                    <div>
                        <label for="preco_padrao" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Preço Padrão (R$) *</label>
                        <input type="number" step="0.01" name="preco_padrao" id="preco_padrao" value="{{ old('preco_padrao', $produto->preco_padrao) }}" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('preco_padrao') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Unidade de Medida --}}
                    <div>
                        <label for="unidade_medida" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Unidade de Medida</label>
                        <input type="text" list="unidades" name="unidade_medida" id="unidade_medida" value="{{ old('unidade_medida', $produto->unidade_medida) }}" placeholder="Ex: UN, KG, HR..." class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        <datalist id="unidades">
                            <option value="UN">Unidade</option>
                            <option value="KG">Quilo</option>
                            <option value="G">Grama</option>
                            <option value="L">Litro</option>
                            <option value="CX">Caixa</option>
                            <option value="PCT">Pacote</option>
                            <option value="HR">Hora</option>
                            <option value="M">Metro</option>
                            <option value="M2">Metro Quadrado</option>
                            <option value="M3">Metro Cúbico</option>
                        </datalist>
                        @error('unidade_medida') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nome --}}
                    <div class="md:col-span-3">
                        <label for="nome" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nome do Produto ou Serviço *</label>
                        <input type="text" name="nome" id="nome" value="{{ old('nome', $produto->nome) }}" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('nome') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="md:col-span-3">
                        <label for="descricao" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Descrição Detalhada</label>
                        <textarea name="descricao" id="descricao" rows="4" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">{{ old('descricao', $produto->descricao) }}</textarea>
                        @error('descricao') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('customer.produtos.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
