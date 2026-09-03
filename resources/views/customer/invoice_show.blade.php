<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('customer.invoices') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors p-1 rounded-lg hover:bg-white/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                    Fatura #{{ $fatura->id }}
                </h2>
                @if($fatura->descricao)
                    <p class="text-sm text-[#8A8F9C] mt-0.5">{{ $fatura->descricao }}</p>
                @endif
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 font-medium text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal (Esquerda) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card 1: Detalhes da Cobrança -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                <h3 class="text-lg font-bold text-[#0A1128] mb-6">Detalhes da Cobrança</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Descrição</dt>
                        <dd class="mt-1 text-base font-bold text-[#0A1128]">{{ $fatura->descricao ?? 'Fatura #' . $fatura->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Valor</dt>
                        <dd class="mt-1 text-base font-bold text-[#0A1128]">R$ {{ number_format($fatura->valor, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Vencimento</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ \Carbon\Carbon::parse($fatura->vencimento)->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full uppercase
                                @if($fatura->status == 'pago') bg-emerald-100 text-emerald-700
                                @elseif($fatura->status == 'atrasado') bg-red-100 text-red-700
                                @elseif($fatura->status == 'cancelado') bg-gray-200 text-gray-700
                                @else bg-orange-100 text-orange-700 @endif">
                                {{ $fatura->status }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Forma de Pagamento</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#0A1128]">
                            @php
                                $formaPgtoLabels = [
                                    'pix' => 'PIX',
                                    'boleto' => 'Boleto',
                                    'transferencia' => 'Transferência',
                                    'link_pagamento' => 'Link de Pagamento',
                                ];
                            @endphp
                            {{ $fatura->forma_pagamento ? ($formaPgtoLabels[$fatura->forma_pagamento] ?? ucfirst(str_replace('_', ' ', $fatura->forma_pagamento))) : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Data de Emissão</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $fatura->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>

                @if(!empty($fatura->observacoes))
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider mb-2">Observações</dt>
                        <dd class="text-sm text-gray-700 bg-[#F4F5F7] rounded-xl p-4 leading-relaxed whitespace-pre-line">{{ $fatura->observacoes }}</dd>
                    </div>
                @endif
            </div>

            <!-- Card 2: Dados para Pagamento -->
            @if(!empty($fatura->dados_bancarios) || !empty($fatura->forma_pagamento) || !empty($fatura->link_pagamento))
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-2.5 mb-4">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <h3 class="text-lg font-bold text-blue-900">Dados para Pagamento</h3>
                    </div>

                    @if($fatura->forma_pagamento)
                        <div class="mb-4 text-sm">
                            <span class="text-xs font-bold text-blue-800 uppercase tracking-wider">Forma de Pagamento:</span>
                            <span class="font-bold text-blue-950 ml-1">
                                @php
                                    $formaPagtoCard = [
                                        'pix' => 'PIX',
                                        'boleto' => 'Boleto',
                                        'transferencia' => 'Transferência Bancária',
                                        'link_pagamento' => 'Link de Pagamento',
                                    ];
                                @endphp
                                {{ $formaPagtoCard[$fatura->forma_pagamento] ?? ucfirst(str_replace('_', ' ', $fatura->forma_pagamento)) }}
                            </span>
                        </div>
                    @endif

                    @if(!empty($fatura->dados_bancarios))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-white/80 rounded-xl p-4 border border-blue-100 mb-4">
                            @if(!empty($fatura->dados_bancarios['titular']))
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Titular:</span>
                                    <p class="font-semibold text-gray-900 text-sm mt-0.5">{{ $fatura->dados_bancarios['titular'] }}</p>
                                </div>
                            @endif
                            @if(!empty($fatura->dados_bancarios['banco']))
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Banco:</span>
                                    <p class="font-semibold text-gray-900 text-sm mt-0.5">{{ $fatura->dados_bancarios['banco'] }}</p>
                                </div>
                            @endif
                            @if(!empty($fatura->dados_bancarios['agencia']))
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Agência:</span>
                                    <p class="font-semibold text-gray-900 text-sm mt-0.5">{{ $fatura->dados_bancarios['agencia'] }}</p>
                                </div>
                            @endif
                            @if(!empty($fatura->dados_bancarios['conta']))
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Conta:</span>
                                    <p class="font-semibold text-gray-900 text-sm mt-0.5">{{ $fatura->dados_bancarios['conta'] }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Chave PIX Section --}}
                        @if(!empty($fatura->dados_bancarios['tipo_chave_pix']) || !empty($fatura->dados_bancarios['chave_pix']))
                            @php
                                $pixLabels = [
                                    'cpf' => 'CPF',
                                    'cnpj' => 'CNPJ',
                                    'email' => 'E-mail',
                                    'telefone' => 'Telefone',
                                    'aleatoria' => 'Chave Aleatória',
                                ];
                                $tipoPixChave = $fatura->dados_bancarios['tipo_chave_pix'] ?? null;
                                $tipoPixDisplay = $tipoPixChave ? ($pixLabels[$tipoPixChave] ?? strtoupper($tipoPixChave)) : 'PIX';
                            @endphp
                            <div class="bg-white rounded-xl p-4 border border-blue-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="text-xs font-bold text-blue-900 uppercase tracking-wider block mb-1">
                                        Chave PIX ({{ $tipoPixDisplay }}):
                                    </span>
                                    <span class="text-sm font-mono font-bold text-[#0A1128] bg-gray-100 px-3 py-1.5 rounded-lg select-all break-all inline-block">
                                        {{ $fatura->dados_bancarios['chave_pix'] ?? '' }}
                                    </span>
                                </div>
                                @if(!empty($fatura->dados_bancarios['chave_pix']))
                                    <button type="button"
                                        onclick="navigator.clipboard.writeText('{{ $fatura->dados_bancarios['chave_pix'] ?? '' }}'); const btn = this; const orig = btn.innerText; btn.innerText = 'Copiado!'; setTimeout(() => btn.innerText = orig, 2000);"
                                        class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors whitespace-nowrap self-start sm:self-center shadow-sm">
                                        Copiar Chave PIX
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endif

                    @if($fatura->link_pagamento)
                        <div class="mt-4 pt-4 border-t border-blue-200 flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <p class="text-sm font-bold text-blue-950">Pagamento Online Disponível</p>
                                <p class="text-xs text-blue-700">Acesse o link seguro para efetuar o pagamento diretamente.</p>
                            </div>
                            <a href="{{ $fatura->link_pagamento }}" target="_blank" class="inline-block bg-[#FF7A1A] text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-[#0A1128] transition-colors shadow-sm">
                                Pagar Agora
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Card 3: Nota Fiscal -->
            @if($fatura->nota_fiscal_path)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-base font-bold text-[#0A1128] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Nota Fiscal
                    </h3>
                    <div class="flex items-center justify-between p-4 bg-[#F4F5F7] rounded-xl border border-gray-100 flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs uppercase">
                                PDF
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#0A1128]">Nota Fiscal de Serviços</p>
                                <p class="text-xs text-[#8A8F9C]">Documento fiscal disponível para download</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $fatura->nota_fiscal_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Baixar Nota Fiscal
                        </a>
                    </div>
                </div>
            @endif

            <!-- Card 4: Comprovante de Pagamento -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-[#0A1128] mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Comprovante de Pagamento
                </h3>

                @if($fatura->comprovante_path)
                    {{-- Comprovante Já Enviado --}}
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-200 text-emerald-800 uppercase">
                                        Comprovante Enviado
                                    </span>
                                </div>
                                @if($fatura->comprovante_enviado_em)
                                    <p class="text-xs text-emerald-700 mt-1">
                                        Enviado em {{ $fatura->comprovante_enviado_em->format('d/m/Y \à\s H:i') }}
                                    </p>
                                @endif
                            </div>
                            <a href="{{ asset('storage/' . $fatura->comprovante_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors shadow-sm self-start sm:self-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Baixar Comprovante
                            </a>
                        </div>
                    </div>

                    {{-- Form para substituir comprovante se fatura não estiver paga nem cancelada --}}
                    @if($fatura->status !== 'pago' && $fatura->status !== 'cancelado')
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <form action="{{ route('customer.invoices.comprovante', $fatura->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label class="block text-sm font-bold text-[#0A1128] mb-1">Substituir Comprovante</label>
                                <p class="text-xs text-[#8A8F9C] mb-4">Envie um novo arquivo caso deseje substituir o comprovante anexado. Formatos: PDF, JPG, PNG (máx. 10MB)</p>
                                <input type="file" name="comprovante" required accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] mb-2 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#0A1128] file:text-white hover:file:bg-[#FF7A1A] file:cursor-pointer border">
                                @error('comprovante')
                                    <p class="text-xs text-red-600 font-semibold mb-3">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="mt-2 bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                                    Substituir Comprovante
                                </button>
                            </form>
                        </div>
                    @elseif($fatura->status === 'pago')
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Fatura paga e comprovante confirmado pela equipe.
                        </div>
                    @endif

                @elseif($fatura->status === 'pago')
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 flex items-center gap-3">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-bold text-sm">Fatura paga</p>
                            <p class="text-xs text-emerald-700">Esta fatura já foi quitada e confirmada no sistema.</p>
                        </div>
                    </div>

                @elseif($fatura->status === 'cancelado')
                    <div class="p-4 bg-gray-100 border border-gray-200 rounded-xl text-gray-700 flex items-center gap-3">
                        <svg class="w-6 h-6 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        <div>
                            <p class="font-bold text-sm">Fatura cancelada</p>
                            <p class="text-xs text-gray-500">Esta fatura foi cancelada e não requer envio de comprovante.</p>
                        </div>
                    </div>

                @else
                    {{-- Sem comprovante e status não é pago nem cancelado --}}
                    <form action="{{ route('customer.invoices.comprovante', $fatura->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="block text-sm font-bold text-[#0A1128] mb-2">Anexar Comprovante de Pagamento</label>
                        <p class="text-xs text-[#8A8F9C] mb-4">Envie o comprovante do pagamento realizado (PIX, boleto, transferência ou outro). Formatos: PDF, JPG, PNG (máx. 10MB)</p>
                        <input type="file" name="comprovante" required accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] mb-2 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#0A1128] file:text-white hover:file:bg-[#FF7A1A] file:cursor-pointer border">
                        @error('comprovante')
                            <p class="text-xs text-red-600 font-semibold mb-3">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="mt-2 bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                            Enviar Comprovante
                        </button>
                    </form>
                @endif
            </div>

        </div>

        <!-- Coluna Lateral (Direita) -->
        <div class="space-y-6">
            <div class="bg-[#0A1128] rounded-2xl p-6 shadow-lg text-white">
                <p class="text-xs font-bold uppercase tracking-wider text-white/60">Valor</p>
                <p class="text-4xl font-extrabold mt-2 tracking-tight">R$ {{ number_format($fatura->valor, 2, ',', '.') }}</p>

                <div class="mt-6 pt-6 border-t border-white/10 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-white/60">Status</span>
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase
                            @if($fatura->status == 'pago') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                            @elseif($fatura->status == 'atrasado') bg-red-500/20 text-red-300 border border-red-500/30
                            @elseif($fatura->status == 'cancelado') bg-gray-500/20 text-gray-300 border border-gray-500/30
                            @else bg-orange-500/20 text-orange-300 border border-orange-500/30 @endif">
                            {{ $fatura->status }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-white/60">Vencimento</span>
                        <span class="text-sm font-semibold text-white">
                            {{ \Carbon\Carbon::parse($fatura->vencimento)->format('d/m/Y') }}
                        </span>
                    </div>

                    @if($fatura->forma_pagamento)
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-white/60">Forma Pgto</span>
                            <span class="text-sm font-semibold text-white">
                                @php
                                    $formaPgtoSide = [
                                        'pix' => 'PIX',
                                        'boleto' => 'Boleto',
                                        'transferencia' => 'Transferência',
                                        'link_pagamento' => 'Link',
                                    ];
                                @endphp
                                {{ $formaPgtoSide[$fatura->forma_pagamento] ?? ucfirst(str_replace('_', ' ', $fatura->forma_pagamento)) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
