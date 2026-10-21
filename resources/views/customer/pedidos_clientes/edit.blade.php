<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Comercial · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">
                    Editar Pedido #{{ $pedido->id }}
                </h2>
            </div>
            <a href="{{ route('customer.pedidos.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar para Pedidos
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Banner de Assinatura Digital / Link Público --}}
        @if($pedido->nome_aprovacao)
            <div class="bg-emerald-50/90 border border-emerald-200 p-5 rounded-2xl shadow-sm flex items-start gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-emerald-900">
                        Pedido aprovado eletronicamente por: <span class="underline">{{ $pedido->nome_aprovacao }}</span>
                    </p>
                    <p class="text-xs text-emerald-700 mt-0.5">
                        Registrado em {{ $pedido->data_aprovacao?->format('d/m/Y \à\s H:i') ?? '-' }} · IP: {{ $pedido->ip_aprovacao ?? '-' }}
                    </p>
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-[#FF7A1A] flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#0A1128]">Aguardando Assinatura Digital</p>
                        <p class="text-xs text-gray-500">Compartilhe o link público para o cliente aprovar a proposta online.</p>
                    </div>
                </div>
                @if($pedido->token_publico)
                    <button type="button"
                            onclick="navigator.clipboard.writeText('{{ route('public.pedido.show', $pedido->token_publico) }}'); alert('Link da proposta copiado!')"
                            class="inline-flex items-center gap-2 bg-[#0A1128] hover:bg-[#141D38] text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm shrink-0">
                        <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Copiar Link do Orçamento
                    </button>
                @endif
            </div>
        @endif

        {{-- Barra de Ações Rápidas --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('customer.pedidos.pdf', $pedido->id) }}" target="_blank" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Baixar PDF
            </a>

            <form action="{{ route('customer.pedidos.enviar-email', $pedido->id) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                    <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Enviar por E-mail
                </button>
            </form>

            @php
                $telefoneLimpo = preg_replace('/[^0-9]/', '', (string) ($pedido->clienteFinal?->telefone ?? ''));
                $nomeContato = $pedido->clienteFinal?->nome_responsavel ?: ($pedido->clienteFinal?->nome_empresa ?? '');
                $textoWhatsApp = urlencode("Olá {$nomeContato}, segue o link da nossa proposta/pedido: " . route('public.pedido.show', $pedido->token_publico ?? ''));
            @endphp
            <a href="https://api.whatsapp.com/send?phone={{ $telefoneLimpo }}&text={{ $textoWhatsApp }}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Enviar no WhatsApp
            </a>
        </div>

        {{-- Formulário Principal --}}
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
            <form action="{{ route('customer.pedidos.update', $pedido->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- Cliente --}}
                    <div class="md:col-span-2">
                        <label for="cliente_final_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Selecione o Cliente *</label>
                        <select name="cliente_final_id" id="cliente_final_id" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            <option value="">Selecione um cliente...</option>
                            @foreach($clientesFinais as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_final_id', $pedido->cliente_final_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome_empresa }} {{ $cliente->nome_responsavel ? '('.$cliente->nome_responsavel.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_final_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status *</label>
                        <select name="status" id="status" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            <option value="Orçamento" {{ old('status', $pedido->status) == 'Orçamento' ? 'selected' : '' }}>Orçamento</option>
                            <option value="Aguardando Pagamento" {{ old('status', $pedido->status) == 'Aguardando Pagamento' ? 'selected' : '' }}>Aguardando Pagamento</option>
                            <option value="Em Andamento" {{ old('status', $pedido->status) == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="Concluído" {{ old('status', $pedido->status) == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                            <option value="Cancelado" {{ old('status', $pedido->status) == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Título --}}
                    <div class="md:col-span-3">
                        <label for="titulo" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Título Geral do Pedido / Projeto *</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $pedido->titulo) }}" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('titulo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Data do Pedido --}}
                    <div>
                        <label for="data_pedido" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Data do Pedido</label>
                        <input type="date" name="data_pedido" id="data_pedido" value="{{ old('data_pedido', $pedido->data_pedido ? $pedido->data_pedido->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('data_pedido') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Data de Entrega --}}
                    <div>
                        <label for="data_entrega" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Previsão de Entrega</label>
                        <input type="date" name="data_entrega" id="data_entrega" value="{{ old('data_entrega', $pedido->data_entrega ? $pedido->data_entrega->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('data_entrega') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="md:col-span-3">
                        <label for="descricao" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Observações / Condições Comerciais</label>
                        <textarea name="descricao" id="descricao" rows="2" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">{{ old('descricao', $pedido->descricao) }}</textarea>
                        @error('descricao') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Itens do Pedido (Carrinho) -->
                <div class="pt-6 border-t border-gray-100 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-display font-extrabold text-lg text-[#0A1128]">Itens do Pedido</h3>
                            <p class="text-xs text-gray-500">Gerencie as linhas, quantidades e valores unitários deste orçamento.</p>
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
                                    <td class="px-4 py-4 text-right font-display font-extrabold text-emerald-600 text-lg" id="valor-total-geral">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</td>
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
                        Salvar Alterações
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
                    <input type="text" name="itens[${itemIndex}][nome_item]" value="${nome}" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] outline-none">
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
            select.value = '';
        }

        function adicionarItemVazio() {
            inserirLinhaHTML('', '', 0, 1);
            recalcularTotalGeral();
        }

        @if(old('itens'))
            @foreach(old('itens') as $item)
                inserirLinhaHTML('{{ $item['produto_servico_id'] ?? '' }}', '{{ addslashes($item['nome_item'] ?? '') }}', {{ (float) ($item['valor_unitario'] ?? 0) }}, {{ (float) ($item['quantidade'] ?? 1) }});
            @endforeach
        @else
            @foreach($pedido->itens as $item)
                inserirLinhaHTML('{{ $item->produto_servico_id ?? '' }}', '{{ addslashes($item->nome_item) }}', {{ (float) $item->valor_unitario }}, {{ (float) $item->quantidade }});
            @endforeach
        @endif

        setTimeout(() => {
            recalcularTotalGeral();
        }, 100);
    </script>
</x-app-layout>
