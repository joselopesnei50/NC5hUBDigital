<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.faturas.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                    Editar Fatura #{{ $fatura->id }}
                </h2>
                <p class="text-sm text-[#8A8F9C] mt-0.5">Atualize os detalhes, status e anexos da cobrança.</p>
            </div>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm max-w-4xl">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Por favor, verifique os erros abaixo:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 mt-1 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-4xl">
        <form action="{{ route('admin.faturas.update', $fatura->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cliente Pagador -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente Pagador</label>
                    <select name="cliente_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('cliente_id', $fatura->cliente_id) == $cliente->id)>
                                {{ $cliente->razao_social }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Descrição da Cobrança -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Descrição da Cobrança</label>
                    <input type="text" name="descricao" value="{{ old('descricao', $fatura->descricao) }}" required placeholder="Ex: Mensalidade Referente ao Mês de Agosto" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <!-- Valor da Fatura -->
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Valor da Fatura (R$)</label>
                    <input type="text" name="valor" id="valor-input" value="{{ old('valor', 'R$ ' . number_format($fatura->valor, 2, ',', '.')) }}" required placeholder="R$ 0,00" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <!-- Data de Vencimento -->
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Vencimento</label>
                    <input type="date" name="vencimento" value="{{ old('vencimento', optional($fatura->vencimento)->format('Y-m-d') ?? $fatura->vencimento) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <!-- Status Field -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status da Fatura</label>
                    <select name="status" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        @foreach(['pendente' => 'Pendente', 'pago' => 'Pago', 'atrasado' => 'Atrasado', 'cancelado' => 'Cancelado'] as $stKey => $stLabel)
                            <option value="{{ $stKey }}" @selected(old('status', $fatura->status) == $stKey)>
                                {{ $stLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Forma de Pagamento -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Forma de Pagamento</label>
                    <select name="forma_pagamento" id="forma-pagamento" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Selecione...</option>
                        <option value="pix" @selected(old('forma_pagamento', $fatura->forma_pagamento) == 'pix')>PIX</option>
                        <option value="boleto" @selected(old('forma_pagamento', $fatura->forma_pagamento) == 'boleto')>Boleto Bancário</option>
                        <option value="transferencia" @selected(old('forma_pagamento', $fatura->forma_pagamento) == 'transferencia')>Transferência Bancária</option>
                        <option value="link_pagamento" @selected(old('forma_pagamento', $fatura->forma_pagamento) == 'link_pagamento')>Link de Pagamento (AbacatePay)</option>
                    </select>
                </div>

                <!-- Dados Bancários Section -->
                <div class="col-span-2" id="dados-bancarios-section" style="display:none">
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h4 class="text-sm font-bold text-[#0A1128] mb-4">Dados Bancários para Pagamento</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Titular</label>
                                <input type="text" name="titular" value="{{ old('titular', $fatura->dados_bancarios['titular'] ?? '') }}" placeholder="Nome do titular da conta" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Banco</label>
                                <input type="text" name="banco" value="{{ old('banco', $fatura->dados_bancarios['banco'] ?? '') }}" placeholder="Ex: Banco do Brasil" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Agência</label>
                                <input type="text" name="agencia" value="{{ old('agencia', $fatura->dados_bancarios['agencia'] ?? '') }}" placeholder="0001" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Conta</label>
                                <input type="text" name="conta" value="{{ old('conta', $fatura->dados_bancarios['conta'] ?? '') }}" placeholder="12345-6" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Tipo de Chave PIX</label>
                                <select name="tipo_chave_pix" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                                    <option value="">Selecione...</option>
                                    @php $tipoPix = old('tipo_chave_pix', $fatura->dados_bancarios['tipo_chave_pix'] ?? ''); @endphp
                                    <option value="cpf" @selected($tipoPix == 'cpf')>CPF</option>
                                    <option value="cnpj" @selected($tipoPix == 'cnpj')>CNPJ</option>
                                    <option value="email" @selected($tipoPix == 'email')>E-mail</option>
                                    <option value="telefone" @selected($tipoPix == 'telefone')>Telefone</option>
                                    <option value="aleatoria" @selected($tipoPix == 'aleatoria')>Chave Aleatória</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Chave PIX</label>
                                <input type="text" name="chave_pix" value="{{ old('chave_pix', $fatura->dados_bancarios['chave_pix'] ?? '') }}" placeholder="Insira a chave PIX" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nota Fiscal -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Nota Fiscal</label>
                    @if($fatura->nota_fiscal_path)
                        <div class="mb-3 p-3.5 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                            <div class="flex items-center gap-3 text-sm text-[#0A1128] font-semibold">
                                <span class="w-8 h-8 rounded-lg bg-orange-100 text-[#FF7A1A] flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-[#0A1128]">Nota Fiscal Anexada</p>
                                    <p class="text-xs text-[#8A8F9C]">Arquivo pronto para consulta</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $fatura->nota_fiscal_path) }}" target="_blank" class="text-xs font-bold text-[#FF7A1A] hover:underline inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 rounded-lg border border-orange-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Baixar / Visualizar
                            </a>
                        </div>
                    @endif
                    <input type="file" name="nota_fiscal" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#0A1128] file:text-white hover:file:bg-[#FF7A1A] file:transition-colors file:cursor-pointer border border-gray-300 rounded-xl p-1 focus:outline-none focus:border-[#0A1128]">
                    <p class="text-xs text-[#8A8F9C] mt-2">
                        Formatos aceitos: PDF, JPG, PNG (máx. 10MB){{ $fatura->nota_fiscal_path ? ' — Envie um novo arquivo para substituir o atual.' : '' }}
                    </p>
                </div>

                <!-- Comprovante do Cliente (se houver) -->
                @if($fatura->comprovante_path)
                    <div class="col-span-2">
                        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-emerald-900">Comprovante Enviado pelo Cliente</p>
                                    <p class="text-xs text-emerald-700">
                                        Enviado em {{ optional($fatura->comprovante_enviado_em)?->format('d/m/Y H:i') ?? '—' }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $fatura->comprovante_path) }}" target="_blank" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 inline-flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-emerald-300 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Ver Comprovante
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Observações -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Observações</label>
                    <textarea name="observacoes" rows="3" placeholder="Observações adicionais para o cliente..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">{{ old('observacoes', $fatura->observacoes) }}</textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-8 flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.faturas.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Salvar
                </button>
            </div>
        </form>

        <!-- Delete Form -->
        <div class="mt-8 pt-6 border-t border-gray-100">
            <form action="{{ route('admin.faturas.destroy', $fatura->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta fatura?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Excluir fatura
                </button>
            </form>
        </div>
    </div>

    <script>
    // Máscara de moeda BRL
    const valorInput = document.getElementById('valor-input');
    if (valorInput) {
        valorInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value === '') { e.target.value = ''; return; }
            value = (parseInt(value) / 100).toFixed(2);
            value = value.replace('.', ',');
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            e.target.value = 'R$ ' + value;
        });
    }

    // Set initial visibility & toggle dados bancários
    const formaPagamento = document.getElementById('forma-pagamento');
    const dadosBancarios = document.getElementById('dados-bancarios-section');
    if (formaPagamento && dadosBancarios) {
        const val = formaPagamento.value;
        dadosBancarios.style.display = (val && val !== 'link_pagamento') ? 'block' : 'none';
        formaPagamento.addEventListener('change', function() {
            dadosBancarios.style.display = (this.value && this.value !== 'link_pagamento') ? 'block' : 'none';
        });
    }
    </script>
</x-admin-layout>
