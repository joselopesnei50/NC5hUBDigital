<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.faturas.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Fatura #{{ $fatura->id }}</h2>
                    <p class="text-sm text-[#8A8F9C] mt-1">{{ $fatura->descricao }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.faturas.enviar_email', $fatura->id) }}" method="POST" class="inline" onsubmit="return confirm('Deseja enviar a notificação desta fatura por e-mail para o cliente?')">
                    @csrf
                    <button type="submit" class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm border border-blue-200 inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Enviar E-mail
                    </button>
                </form>
                <a href="{{ route('admin.faturas.edit', $fatura->id) }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $formasMap = [
            'pix' => 'PIX',
            'boleto' => 'Boleto Bancário',
            'transferencia' => 'Transferência Bancária',
            'link_pagamento' => 'Link de Pagamento (AbacatePay)',
        ];
        $formaLabel = $fatura->forma_pagamento ? ($formasMap[$fatura->forma_pagamento] ?? ucfirst($fatura->forma_pagamento)) : '—';

        $tiposPixMap = [
            'cpf' => 'CPF',
            'cnpj' => 'CNPJ',
            'email' => 'E-mail',
            'telefone' => 'Telefone',
            'aleatoria' => 'Chave Aleatória',
        ];
        $tipoPixKey = $fatura->dados_bancarios['tipo_chave_pix'] ?? null;
        $tipoPixLabel = $tipoPixKey ? ($tiposPixMap[$tipoPixKey] ?? strtoupper($tipoPixKey)) : '—';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Esquerda -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Card 1: Dados da Cobrança -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                <h3 class="text-lg font-bold text-[#0A1128] mb-6">Dados da Cobrança</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Cliente</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $fatura->cliente->razao_social ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">E-mail</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $fatura->cliente->user->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Vencimento</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#0A1128]">
                            {{ $fatura->vencimento ? \Carbon\Carbon::parse($fatura->vencimento)->format('d/m/Y') : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Emissão</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $fatura->created_at->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Forma de Pagamento</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $formaLabel }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Observações</dt>
                        <dd class="mt-1 text-sm text-[#0A1128] bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">{{ $fatura->observacoes ?: '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Card 2: Dados Bancários para Pagamento (se houver) -->
            @if($fatura->dados_bancarios)
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-[#0A1128] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Dados Bancários para Pagamento
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-xs font-bold text-blue-900/60 uppercase">Titular</dt>
                            <dd class="mt-0.5 font-semibold text-[#0A1128]">{{ $fatura->dados_bancarios['titular'] ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-blue-900/60 uppercase">Banco</dt>
                            <dd class="mt-0.5 font-semibold text-[#0A1128]">{{ $fatura->dados_bancarios['banco'] ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-blue-900/60 uppercase">Agência</dt>
                            <dd class="mt-0.5 font-semibold text-[#0A1128]">{{ $fatura->dados_bancarios['agencia'] ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-blue-900/60 uppercase">Conta</dt>
                            <dd class="mt-0.5 font-semibold text-[#0A1128]">{{ $fatura->dados_bancarios['conta'] ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-blue-900/60 uppercase">Tipo Chave PIX</dt>
                            <dd class="mt-0.5 font-semibold text-[#0A1128]">{{ $tipoPixLabel }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-blue-900/60 uppercase">Chave PIX</dt>
                            <dd class="mt-0.5 font-semibold text-[#0A1128] break-all">{{ $fatura->dados_bancarios['chave_pix'] ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

            <!-- Card 3: Nota Fiscal (se houver) -->
            @if($fatura->nota_fiscal_path)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-orange-100 text-[#FF7A1A] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-[#0A1128]">Nota Fiscal</h3>
                            <p class="text-xs text-[#8A8F9C]">Documento fiscal anexado à fatura</p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $fatura->nota_fiscal_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Baixar Nota Fiscal
                    </a>
                </div>
            @endif

            <!-- Card 4: Comprovante do Cliente (se houver) -->
            @if($fatura->comprovante_path)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div>
                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase bg-emerald-200 text-emerald-800">
                                Comprovante Recebido
                            </span>
                        </div>
                        <p class="text-xs text-emerald-800 pt-1">
                            Enviado pelo cliente em {{ optional($fatura->comprovante_enviado_em)?->format('d/m/Y \à\s H:i') ?? '—' }}
                        </p>
                    </div>
                    <a href="{{ asset('storage/' . $fatura->comprovante_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors shadow self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Baixar Comprovante
                    </a>
                </div>
            @endif
        </div>

        <!-- Coluna Direita -->
        <div class="space-y-6">
            <div class="bg-[#0A1128] rounded-2xl p-6 shadow-lg text-white">
                <p class="text-xs font-bold uppercase tracking-wider text-white/60">Valor Total</p>
                <p class="text-4xl font-extrabold mt-2 text-[#FF7A1A]">
                    R$ {{ number_format($fatura->valor, 2, ',', '.') }}
                </p>

                <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between">
                    <span class="text-xs text-white/60 font-medium">Status</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase
                        {{ $fatura->status == 'pago' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($fatura->status == 'atrasado' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : ($fatura->status == 'cancelado' ? 'bg-gray-500/20 text-gray-300 border border-gray-500/30' : 'bg-orange-500/20 text-orange-300 border border-orange-500/30')) }}">
                        {{ $fatura->status }}
                    </span>
                </div>

                @if(!empty($fatura->link_pagamento))
                    <div class="mt-6 pt-4 border-t border-white/10">
                        <p class="text-xs text-white/60 mb-2">Checkout Online (AbacatePay)</p>
                        <a href="{{ $fatura->link_pagamento }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 bg-[#FF7A1A] hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-colors shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Abrir Link de Pagamento
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
