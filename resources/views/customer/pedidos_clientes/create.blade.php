<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Novo Pedido / Orçamento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('customer.pedidos.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
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

                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label for="titulo" class="block font-medium text-sm text-gray-700">Título Geral do Pedido / Projeto *</label>
                                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required placeholder="Ex: Pacote de Marketing e Site" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                <label for="descricao" class="block font-medium text-sm text-gray-700">Observações / Detalhes</label>
                                <textarea name="descricao" id="descricao" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao') }}</textarea>
                                @error('descricao') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <hr class="my-6 border-gray-200">

                        <!-- Itens do Pedido (Carrinho) -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Itens do Pedido</h3>
                            
                            @error('itens') <p class="text-red-500 text-sm mb-2 font-semibold">Adicione pelo menos um item ao pedido.</p> @enderror

                            <!-- Adicionar Produto Rápido -->
                            <div class="flex gap-2 mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Puxar do Catálogo</label>
                                    <select id="select_catalogo" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">Selecione um produto/serviço para adicionar...</option>
                                        @foreach($produtosServicos as $produto)
                                            <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_padrao }}" data-nome="{{ $produto->nome }}">
                                                [{{ $produto->tipo }}] {{ $produto->nome }} - R$ {{ number_format($produto->preco_padrao, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="adicionarDoCatalogo()" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm font-semibold transition">
                                        + Adicionar Item
                                    </button>
                                </div>
                                <div class="flex items-end ml-4">
                                    <button type="button" onclick="adicionarItemVazio()" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-semibold transition">
                                        + Item Avulso
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Item / Descrição</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">V. Unitário (R$)</th>
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total (R$)</th>
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabela-itens" class="bg-white divide-y divide-gray-200">
                                        <!-- Itens injetados via JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50">
                                            <td colspan="3" class="px-4 py-4 text-right font-bold text-gray-700">Total do Pedido:</td>
                                            <td class="px-4 py-4 text-right font-bold text-green-600 text-lg" id="valor-total-geral">R$ 0,00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('customer.pedidos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Salvar Pedido
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        let itemIndex = 0;

        function formatarMoeda(valor) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
        }

        function recalcularTotalItem(index) {
            let qtd = parseFloat(document.getElementById(`qtd_${index}`).value) || 0;
            let unitario = parseFloat(document.getElementById(`unitario_${index}`).value) || 0;
            let total = qtd * unitario;
            document.getElementById(`total_label_${index}`).innerText = formatarMoeda(total);
            recalcularTotalGeral();
        }

        function recalcularTotalGeral() {
            let trs = document.querySelectorAll('#tabela-itens tr');
            let soma = 0;
            trs.forEach(tr => {
                let idx = tr.getAttribute('data-index');
                let qtd = parseFloat(document.getElementById(`qtd_${idx}`).value) || 0;
                let unitario = parseFloat(document.getElementById(`unitario_${idx}`).value) || 0;
                soma += (qtd * unitario);
            });
            document.getElementById('valor-total-geral').innerText = formatarMoeda(soma);
        }

        function removerItem(index) {
            let tr = document.getElementById(`linha_${index}`);
            if(tr) {
                tr.remove();
                recalcularTotalGeral();
            }
        }

        function inserirLinhaHTML(produtoId, nome, preco, qtd = 1) {
            let tbody = document.getElementById('tabela-itens');
            let totalItem = preco * qtd;

            let tr = document.createElement('tr');
            tr.id = `linha_${itemIndex}`;
            tr.setAttribute('data-index', itemIndex);

            let html = `
                <td class="px-4 py-2">
                    <input type="hidden" name="itens[${itemIndex}][produto_servico_id]" value="${produtoId}">
                    <input type="text" name="itens[${itemIndex}][nome_item]" value="${nome}" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
                </td>
                <td class="px-4 py-2 w-24">
                    <input type="number" step="0.01" min="0.01" name="itens[${itemIndex}][quantidade]" id="qtd_${itemIndex}" value="${qtd}" oninput="recalcularTotalItem(${itemIndex})" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
                </td>
                <td class="px-4 py-2 w-32">
                    <input type="number" step="0.01" min="0" name="itens[${itemIndex}][valor_unitario]" id="unitario_${itemIndex}" value="${preco.toFixed(2)}" oninput="recalcularTotalItem(${itemIndex})" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
                </td>
                <td class="px-4 py-2 text-right font-medium text-gray-700" id="total_label_${itemIndex}">
                    ${formatarMoeda(totalItem)}
                </td>
                <td class="px-4 py-2 text-right">
                    <button type="button" onclick="removerItem(${itemIndex})" class="text-red-500 hover:text-red-700 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </td>
            `;

            tr.innerHTML = html;
            tbody.appendChild(tr);

            itemIndex++;
            recalcularTotalGeral();
        }

        function adicionarDoCatalogo() {
            let select = document.getElementById('select_catalogo');
            let option = select.options[select.selectedIndex];
            
            if(!option.value) {
                alert('Selecione um produto do catálogo primeiro.');
                return;
            }

            let produtoId = option.value;
            let nome = option.getAttribute('data-nome');
            let preco = parseFloat(option.getAttribute('data-preco')) || 0;

            inserirLinhaHTML(produtoId, nome, preco, 1);
            select.value = ''; // reseta
        }

        function adicionarItemVazio() {
            inserirLinhaHTML('', '', 0, 1);
        }

        // Se houver inputs antigos de validação falha
        @if(old('itens'))
            @foreach(old('itens') as $item)
                inserirLinhaHTML('{{ $item['produto_servico_id'] ?? '' }}', '{{ $item['nome_item'] }}', {{ $item['valor_unitario'] }}, {{ $item['quantidade'] }});
            @endforeach
        @endif
    </script>
</x-app-layout>
