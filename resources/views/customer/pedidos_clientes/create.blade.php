<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Novo Pedido') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('customer.pedidos.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Cliente --}}
                            <div class="md:col-span-2">
                                <label for="cliente_final_id" class="block font-medium text-sm text-gray-700">Selecione o Cliente *</label>
                                <select name="cliente_final_id" id="cliente_final_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Selecione um cliente...</option>
                                    @foreach($clientesFinais as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_final_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome_empresa }} {{ $cliente->nome_responsavel ? '('.$cliente->nome_responsavel.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_final_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Produto / Serviço --}}
                            <div class="md:col-span-2">
                                <label for="produto_servico_id" class="block font-medium text-sm text-gray-700">Vincular a um Produto/Serviço do Catálogo</label>
                                <select name="produto_servico_id" id="produto_servico_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="autoFillProduto(this)">
                                    <option value="">Nenhum (Pedido Avulso)</option>
                                    @foreach($produtosServicos as $produto)
                                        <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_padrao }}" data-nome="{{ $produto->nome }}" {{ old('produto_servico_id') == $produto->id ? 'selected' : '' }}>
                                            [{{ $produto->tipo }}] {{ $produto->nome }} - R$ {{ number_format($produto->preco_padrao, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('produto_servico_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label for="titulo" class="block font-medium text-sm text-gray-700">Título ou Identificador do Pedido *</label>
                                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required placeholder="Ex: Criação de Site Institucional" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Valor --}}
                            <div>
                                <label for="valor" class="block font-medium text-sm text-gray-700">Valor Negociado (R$) *</label>
                                <input type="number" step="0.01" name="valor" id="valor" value="{{ old('valor', '0.00') }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="text-xs text-gray-500 mt-1">Você pode alterar este valor se der desconto.</p>
                                @error('valor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700">Status *</label>
                                <select name="status" id="status" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Orçamento" {{ old('status') == 'Orçamento' ? 'selected' : '' }}>Orçamento</option>
                                    <option value="Aguardando Pagamento" {{ old('status') == 'Aguardando Pagamento' ? 'selected' : '' }}>Aguardando Pagamento</option>
                                    <option value="Em Andamento" {{ old('status') == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
                                    <option value="Concluído" {{ old('status') == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                                    <option value="Cancelado" {{ old('status') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Data do Pedido --}}
                            <div>
                                <label for="data_pedido" class="block font-medium text-sm text-gray-700">Data do Pedido</label>
                                <input type="date" name="data_pedido" id="data_pedido" value="{{ old('data_pedido', date('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('data_pedido') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Data de Entrega --}}
                            <div>
                                <label for="data_entrega" class="block font-medium text-sm text-gray-700">Previsão de Entrega</label>
                                <input type="date" name="data_entrega" id="data_entrega" value="{{ old('data_entrega') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('data_entrega') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Descrição --}}
                            <div class="md:col-span-2">
                                <label for="descricao" class="block font-medium text-sm text-gray-700">Descrição / Escopo / Detalhes</label>
                                <textarea name="descricao" id="descricao" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao') }}</textarea>
                                @error('descricao') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('customer.pedidos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Criar Pedido
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function autoFillProduto(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            if (selectedOption.value) {
                const preco = selectedOption.getAttribute('data-preco');
                const nome = selectedOption.getAttribute('data-nome');
                
                document.getElementById('valor').value = preco;
                if(document.getElementById('titulo').value === '') {
                    document.getElementById('titulo').value = nome;
                }
            }
        }
    </script>
</x-app-layout>
