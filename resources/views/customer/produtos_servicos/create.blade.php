<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Novo Produto / Serviço') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('customer.produtos.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Tipo --}}
                            <div>
                                <label for="tipo" class="block font-medium text-sm text-gray-700">Tipo *</label>
                                <select name="tipo" id="tipo" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Serviço" {{ old('tipo') == 'Serviço' ? 'selected' : '' }}>Serviço</option>
                                    <option value="Produto" {{ old('tipo') == 'Produto' ? 'selected' : '' }}>Produto</option>
                                </select>
                                @error('tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Preço Padrão --}}
                            <div>
                                <label for="preco_padrao" class="block font-medium text-sm text-gray-700">Preço Padrão (R$) *</label>
                                <input type="number" step="0.01" name="preco_padrao" id="preco_padrao" value="{{ old('preco_padrao', '0.00') }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('preco_padrao') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Unidade de Medida --}}
                            <div>
                                <label for="unidade_medida" class="block font-medium text-sm text-gray-700">Unidade de Medida</label>
                                <input type="text" list="unidades" name="unidade_medida" id="unidade_medida" value="{{ old('unidade_medida') }}" placeholder="Ex: UN, KG, HR..." class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
                                @error('unidade_medida') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nome --}}
                            <div class="md:col-span-2">
                                <label for="nome" class="block font-medium text-sm text-gray-700">Nome do Produto ou Serviço *</label>
                                <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required placeholder="Ex: Gestão de Tráfego, Criação de Site..." class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('nome') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Descrição --}}
                            <div class="md:col-span-2">
                                <label for="descricao" class="block font-medium text-sm text-gray-700">Descrição Detalhada</label>
                                <textarea name="descricao" id="descricao" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao') }}</textarea>
                                @error('descricao') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('customer.produtos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Salvar no Catálogo
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
