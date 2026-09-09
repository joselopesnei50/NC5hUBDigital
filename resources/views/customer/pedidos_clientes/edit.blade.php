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
                <a href="{{ route('customer.pedidos.pdf', $pedido->id) }}" target="_blank" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-900 flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Baixar PDF
                </a>

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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
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

                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label for="titulo" class="block font-medium text-sm text-gray-700">Título Geral do Pedido / Projeto *</label>
                                <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $pedido->titulo) }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                <label for="descricao" class="block font-medium text-sm text-gray-700">Observações / Detalhes</label>
                                <textarea name="descricao" id="descricao" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao', $pedido->descricao) }}</textarea>
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
                                            <td class="px-4 py-4 text-right font-bold text-green-600 text-lg" id="valor-total-geral">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
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
            recalcularTotalGeral();
            select.value = ''; // reseta
        }

        function adicionarItemVazio() {
            inserirLinhaHTML('', '', 0, 1);
            recalcularTotalGeral();
        }

        // Preencher itens existentes
        @if(old('itens'))
            @foreach(old('itens') as $item)
                inserirLinhaHTML('{{ $item['produto_servico_id'] ?? '' }}', '{{ addslashes($item['nome_item']) }}', {{ $item['valor_unitario'] }}, {{ $item['quantidade'] }});
            @endforeach
        @else
            @foreach($pedido->itens as $item)
                inserirLinhaHTML('{{ $item->produto_servico_id ?? '' }}', '{{ addslashes($item->nome_item) }}', {{ $item->valor_unitario }}, {{ $item->quantidade }});
            @endforeach
        @endif
        
        // Atualizar total ao iniciar
        setTimeout(() => {
            recalcularTotalGeral();
        }, 100);
    </script>
</x-app-layout>
