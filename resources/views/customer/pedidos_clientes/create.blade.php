<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Comercial · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">
                    Novo Pedido / Orçamento
                </h2>
            </div>
            <a href="{{ route('customer.pedidos.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar para Pedidos
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
            <div class="mb-6 pb-5 border-b border-gray-100">
                <h3 class="font-display font-extrabold text-lg text-[#0A1128]">Dados da Proposta Comercial</h3>
                <p class="text-sm text-gray-500 mt-0.5">Monte o orçamento ou pedido selecionando itens do seu catálogo ou inserindo itens personalizados.</p>
            </div>

            <form action="{{ route('customer.pedidos.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- Cliente --}}
                    <div class="md:col-span-2">
                        <label for="cliente_final_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Selecione o Cliente *</label>
                        <select name="cliente_final_id" id="cliente_final_id" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            <option value="">Selecione um cliente...</option>
                            @foreach($clientesFinais as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_final_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome_empresa }} {{ $cliente->nome_responsavel ? '('.$cliente->nome_responsavel.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_final_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Inicial *</label>
                        <select name="status" id="status" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            <option value="Orçamento" {{ old('status') == 'Orçamento' ? 'selected' : '' }}>Orçamento</option>
                            <option value="Aguardando Pagamento" {{ old('status') == 'Aguardando Pagamento' ? 'selected' : '' }}>Aguardando Pagamento</option>
                            <option value="Em Andamento" {{ old('status') == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="Concluído" {{ old('status') == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                            <option value="Cancelado" {{ old('status') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Título --}}
                    <div class="md:col-span-3">
                        <label for="titulo" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Título Geral do Pedido / Projeto *</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required placeholder="Ex: Proposta de Consultoria e Implementação" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('titulo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Data do Pedido --}}
                    <div>
                        <label for="data_pedido" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Data do Pedido</label>
                        <input type="date" name="data_pedido" id="data_pedido" value="{{ old('data_pedido', date('Y-m-d')) }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('data_pedido') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Data de Entrega --}}
                    <div>
                        <label for="data_entrega" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Previsão de Entrega</label>
                        <input type="date" name="data_entrega" id="data_entrega" value="{{ old('data_entrega') }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('data_entrega') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="md:col-span-3">
                        <label for="descricao" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Observações / Condições Comerciais</label>
                        <textarea name="descricao" id="descricao" rows="2" placeholder="Condições de pagamento, prazos ou observações gerais..." class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">{{ old('descricao') }}</textarea>
                        @error('descricao') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Itens do Pedido (Carrinho) -->
                <div class="pt-6 border-t border-gray-100 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-display font-extrabold text-lg text-[#0A1128]">Itens do Pedido</h3>
                            <p class="text-xs text-gray-500">Adicione itens do seu catálogo ou crie linhas avulsas.</p>
                        </div>
                    </div>

                    @error('itens')
                        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-xs font-bold text-red-600">
                            Adicione pelo menos um item ao pedido.
                        </div>
                    @enderror

                    <!-- Adicionar Produto Rápido -->
                    <div class="flex flex-col sm:flex-row gap-3 mb-6 bg-gray-50/80 p-4 rounded-2xl border border-gray-200/80">
                        <div class="flex-1">
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1.5">Puxar do Catálogo</label>
                            <select id="select_catalogo" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-[#0A1128] focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                                <option value="">Selecione um produto ou serviço para adicionar...</option>
                                @foreach($produtosServicos as $produto)
                                    <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_padrao }}" data-nome="{{ $produto->nome }}">
                                        [{{ $produto->tipo }}] {{ $produto->nome }} - R$ {{ number_format($produto->preco_padrao, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="button" onclick="adicionarDoCatalogo()" class="bg-[#0A1128] hover:bg-[#141D38] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                                + Adicionar do Catálogo
                            </button>
                            <button type="button" onclick="adicionarItemVazio()" class="bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                                + Item Avulso
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-gray-200/80">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider w-1/2">Item / Descrição</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Qtd.</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">V. Unitário (R$)</th>
                                    <th scope="col" class="px-4 py-3.5 text-right text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    <th scope="col" class="px-4 py-3.5 text-right text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="tabela-itens" class="bg-white divide-y divide-gray-100">
                                <!-- Itens injetados via JS -->
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50/80 border-t border-gray-200">
                                    <td colspan="3" class="px-4 py-4 text-right text-xs font-extrabold uppercase tracking-wider text-gray-600">Total do Pedido:</td>
                                    <td class="px-4 py-4 text-right font-display font-extrabold text-emerald-600 text-lg" id="valor-total-geral">R$ 0,00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('customer.pedidos.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                        Salvar Pedido
                    </button>
                </div>
            </form>
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
            let precoNum = parseFloat(preco) || 0;
            let qtdNum = parseFloat(qtd) || 1;
            let totalItem = precoNum * qtdNum;

            let tr = document.createElement('tr');
            tr.id = `linha_${itemIndex}`;
            tr.setAttribute('data-index', itemIndex);

            let html = `
                <td class="px-4 py-2.5">
                    <input type="hidden" name="itens[${itemIndex}][produto_servico_id]" value="${produtoId}">
                    <input type="text" name="itens[${itemIndex}][nome_item]" value="${nome}" required placeholder="Descrição do item" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] outline-none">
                </td>
                <td class="px-4 py-2.5 w-28">
                    <input type="number" step="0.01" min="0.01" name="itens[${itemIndex}][quantidade]" id="qtd_${itemIndex}" value="${qtdNum}" oninput="recalcularTotalItem(${itemIndex})" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] outline-none">
                </td>
                <td class="px-4 py-2.5 w-36">
                    <input type="number" step="0.01" min="0" name="itens[${itemIndex}][valor_unitario]" id="unitario_${itemIndex}" value="${precoNum.toFixed(2)}" oninput="recalcularTotalItem(${itemIndex})" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] outline-none">
                </td>
                <td class="px-4 py-2.5 text-right font-bold text-[#0A1128] text-sm" id="total_label_${itemIndex}">
                    ${formatarMoeda(totalItem)}
                </td>
                <td class="px-4 py-2.5 text-right">
                    <button type="button" onclick="removerItem(${itemIndex})" class="text-gray-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
            select.value = '';
        }

        function adicionarItemVazio() {
            inserirLinhaHTML('', '', 0, 1);
        }

        @if(old('itens'))
            @foreach(old('itens') as $item)
                inserirLinhaHTML('{{ $item['produto_servico_id'] ?? '' }}', '{{ addslashes($item['nome_item'] ?? '') }}', {{ (float) ($item['valor_unitario'] ?? 0) }}, {{ (float) ($item['quantidade'] ?? 1) }});
            @endforeach
        @endif
    </script>
</x-app-layout>
