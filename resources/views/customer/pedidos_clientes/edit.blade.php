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
                    @if($pedido->token_publico)
                        <button type="button" onclick="navigator.clipboard.writeText('{{ route('public.pedido.show', $pedido->token_publico) }}'); alert('Copiado!')" class="bg-blue-600 text-white px-3 py-1 text-xs rounded shadow hover:bg-blue-700">Copiar Link do Orçamento</button>
                    @endif
                </div>
            @endif

            <div class="mb-6 flex flex-wrap gap-4">
                <form action="{{ route('customer.pedidos.enviar-email', $pedido->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 flex items-center transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Enviar por E-mail
                    </button>
                </form>

                @php
                    $telefoneLimpo = preg_replace('/[^0-9]/', '', $pedido->clienteFinal->telefone);
                    $textoWhatsApp = urlencode("Olá {$pedido->clienteFinal->nome_responsavel}, segue o link da nossa proposta/pedido: " . route('public.pedido.show', $pedido->token_publico ?? ''));
                @endphp
                <a href="https://api.whatsapp.com/send?phone={{ $telefoneLimpo }}&text={{ $textoWhatsApp }}" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600 flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Enviar por WhatsApp
                </a>
            </div>

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
