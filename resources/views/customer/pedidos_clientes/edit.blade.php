<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Pedido') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($pedido->nome_aprovacao)
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Pedido aprovado eletronicamente por:</strong> {{ $pedido->nome_aprovacao }}<br>
                                <span class="text-xs">Registrado em {{ $pedido->data_aprovacao->format('d/m/Y \à\s H:i') }} pelo IP: {{ $pedido->ip_aprovacao }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md shadow-sm flex items-center justify-between">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">Este pedido ainda não foi assinado digitalmente pelo cliente final.</p>
                        </div>
                    </div>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ route('public.pedido.show', $pedido->token_publico) }}'); alert('Copiado!')" class="bg-blue-600 text-white px-3 py-1 text-xs rounded shadow hover:bg-blue-700">Copiar Link do Orçamento</button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('customer.pedidos.update', $pedido->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Cliente --}}
                            <div class="md:col-span-2">
                                <label for="cliente_final_id" class="block font-medium text-sm text-gray-700">Selecione o Cliente *</label>
                                <select name="cliente_final_id" id="cliente_final_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Selecione um cliente...</option>
                                    @foreach($clientesFinais as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_final_id', $pedido->cliente_final_id) == $cliente->id ? 'selected' : '' }}>
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
                                        <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_padrao }}" data-nome="{{ $produto->nome }}" {{ old('produto_servico_id', $pedido->produto_servico_id) == $produto->id ? 'selected' : '' }}>
                                            [{{ $produto->tipo }}] {{ $produto->nome }} - R$ {{ number_format($produto->preco_padrao, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('produto_servico_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label for="titulo" class="block font-medium text-sm text-gray-700">Título ou Identificador do Pedido *</label>
                                <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $pedido->titulo) }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Valor --}}
                            <div>
                                <label for="valor" class="block font-medium text-sm text-gray-700">Valor Negociado (R$) *</label>
                                <input type="number" step="0.01" name="valor" id="valor" value="{{ old('valor', $pedido->valor) }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="text-xs text-gray-500 mt-1">Você pode alterar este valor se der desconto.</p>
                                @error('valor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700">Status *</label>
                                <select name="status" id="status" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Orçamento" {{ old('status', $pedido->status) == 'Orçamento' ? 'selected' : '' }}>Orçamento</option>
                                    <option value="Aguardando Pagamento" {{ old('status', $pedido->status) == 'Aguardando Pagamento' ? 'selected' : '' }}>Aguardando Pagamento</option>
                                    <option value="Em Andamento" {{ old('status', $pedido->status) == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
                                    <option value="Concluído" {{ old('status', $pedido->status) == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                                    <option value="Cancelado" {{ old('status', $pedido->status) == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Data do Pedido --}}
                            <div>
                                <label for="data_pedido" class="block font-medium text-sm text-gray-700">Data do Pedido</label>
                                <input type="date" name="data_pedido" id="data_pedido" value="{{ old('data_pedido', $pedido->data_pedido ? $pedido->data_pedido->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('data_pedido') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Data de Entrega --}}
                            <div>
                                <label for="data_entrega" class="block font-medium text-sm text-gray-700">Previsão de Entrega</label>
                                <input type="date" name="data_entrega" id="data_entrega" value="{{ old('data_entrega', $pedido->data_entrega ? $pedido->data_entrega->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('data_entrega') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Descrição --}}
                            <div class="md:col-span-2">
                                <label for="descricao" class="block font-medium text-sm text-gray-700">Descrição / Escopo / Detalhes</label>
                                <textarea name="descricao" id="descricao" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao', $pedido->descricao) }}</textarea>
                                @error('descricao') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('customer.pedidos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Salvar Alterações
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
            }
        }
    </script>
</x-app-layout>
