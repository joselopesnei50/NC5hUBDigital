<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.faturas.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                    Gerar Nova Fatura
                </h2>
                <p class="text-sm text-[#8A8F9C] mt-0.5">Preencha as informações para emitir uma nova cobrança.</p>
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
        <form action="{{ route('admin.faturas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Cliente Pagador -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente Pagador</label>
                    <select name="cliente_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Selecione o Cliente...</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>
                                {{ $cliente->razao_social }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Descrição da Cobrança -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Descrição da Cobrança</label>
                    <input type="text" name="descricao" value="{{ old('descricao') }}" required placeholder="Ex: Mensalidade Referente ao Mês de Agosto" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <!-- 3. Valor da Fatura -->
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Valor da Fatura (R$)</label>
                    <input type="text" name="valor" id="valor-input" value="{{ old('valor') }}" required placeholder="R$ 0,00" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <!-- 4. Data de Vencimento -->
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de Vencimento</label>
                    <input type="date" name="vencimento" value="{{ old('vencimento') }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <!-- 5. Forma de Pagamento -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Forma de Pagamento</label>
                    <select name="forma_pagamento" id="forma-pagamento" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Selecione...</option>
                        <option value="pix" @selected(old('forma_pagamento') == 'pix')>PIX</option>
                        <option value="boleto" @selected(old('forma_pagamento') == 'boleto')>Boleto Bancário</option>
                        <option value="transferencia" @selected(old('forma_pagamento') == 'transferencia')>Transferência Bancária</option>
                        <option value="link_pagamento" @selected(old('forma_pagamento') == 'link_pagamento')>Link de Pagamento (AbacatePay)</option>
                    </select>
                </div>

                <!-- 6. Dados Bancários Section -->
                <div class="col-span-2" id="dados-bancarios-section" style="display:none">
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h4 class="text-sm font-bold text-[#0A1128] mb-4">Dados Bancários para Pagamento</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Titular</label>
                                <input type="text" name="titular" value="{{ old('titular') }}" placeholder="Nome do titular da conta" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Banco</label>
                                <input type="text" name="banco" value="{{ old('banco') }}" placeholder="Ex: Banco do Brasil" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Agência</label>
                                <input type="text" name="agencia" value="{{ old('agencia') }}" placeholder="0001" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Conta</label>
                                <input type="text" name="conta" value="{{ old('conta') }}" placeholder="12345-6" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Tipo de Chave PIX</label>
                                <select name="tipo_chave_pix" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                                    <option value="">Selecione...</option>
                                    <option value="cpf" @selected(old('tipo_chave_pix') == 'cpf')>CPF</option>
                                    <option value="cnpj" @selected(old('tipo_chave_pix') == 'cnpj')>CNPJ</option>
                                    <option value="email" @selected(old('tipo_chave_pix') == 'email')>E-mail</option>
                                    <option value="telefone" @selected(old('tipo_chave_pix') == 'telefone')>Telefone</option>
                                    <option value="aleatoria" @selected(old('tipo_chave_pix') == 'aleatoria')>Chave Aleatória</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0A1128] mb-2">Chave PIX</label>
                                <input type="text" name="chave_pix" value="{{ old('chave_pix') }}" placeholder="Insira a chave PIX" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 7. Anexar Nota Fiscal -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Anexar Nota Fiscal</label>
                    <input type="file" name="nota_fiscal" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#0A1128] file:text-white hover:file:bg-[#FF7A1A] file:transition-colors file:cursor-pointer border border-gray-300 rounded-xl p-1 focus:outline-none focus:border-[#0A1128]">
                    <p class="text-xs text-[#8A8F9C] mt-2">Formatos aceitos: PDF, JPG, PNG (máx. 10MB)</p>
                </div>

                <!-- 8. Observações -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Observações</label>
                    <textarea name="observacoes" rows="3" placeholder="Observações adicionais para o cliente..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">{{ old('observacoes') }}</textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-8 flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.faturas.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Gerar Fatura
                </button>
            </div>
        </form>
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

    // Toggle dados bancários
    const formaPagamento = document.getElementById('forma-pagamento');
    const dadosBancarios = document.getElementById('dados-bancarios-section');
    if (formaPagamento && dadosBancarios) {
        if (formaPagamento.value && formaPagamento.value !== 'link_pagamento') {
            dadosBancarios.style.display = 'block';
        }
        formaPagamento.addEventListener('change', function() {
            dadosBancarios.style.display = (this.value && this.value !== 'link_pagamento') ? 'block' : 'none';
        });
    }
    </script>
</x-admin-layout>
