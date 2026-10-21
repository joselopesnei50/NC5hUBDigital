<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $nomeEmissor = $pedido->cliente?->nome_fantasia ?: ($pedido->cliente?->razao_social ?? 'Empresa');
        $nomeDestinatario = $pedido->clienteFinal?->nome_empresa ?: ($pedido->clienteFinal?->nome_responsavel ?? 'Cliente');
    @endphp
    <title>Proposta Comercial #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }} — {{ $nomeEmissor }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|fraunces:600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        .font-display { font-family: 'Fraunces', Georgia, serif; }
    </style>
</head>
<body class="bg-[#F4F5F7] text-[#0A1128] py-8 sm:py-12 px-4">
    <div class="max-w-4xl mx-auto">

        {{-- Alertas de Feedback --}}
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-900 px-5 py-4 rounded-2xl shadow-sm flex items-center gap-3 text-sm font-semibold">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl shadow-sm flex items-center gap-3 text-sm font-semibold">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl shadow-sm text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Documento Principal --}}
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xl overflow-hidden">

            {{-- Cabeçalho Executivo --}}
            <div class="bg-[#0A1128] text-white p-6 sm:p-10 relative overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 relative z-10">
                    <div>
                        <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-2">
                            Documento Comercial Oficial
                        </span>
                        <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-white leading-tight">
                            {{ $nomeEmissor }}
                        </h1>
                        @if($pedido->cliente?->cpf_cnpj)
                            <p class="text-xs text-gray-400 mt-1">CNPJ / Doc: {{ $pedido->cliente->cpf_cnpj }}</p>
                        @endif
                        @if($pedido->cliente?->telefone)
                            <p class="text-xs text-gray-400 mt-0.5">Contato: {{ $pedido->cliente->telefone }}</p>
                        @endif
                    </div>

                    <div class="sm:text-right flex flex-col sm:items-end">
                        <span class="text-[11px] font-extrabold uppercase tracking-widest text-gray-400">Proposta Nº</span>
                        <div class="font-display font-extrabold text-2xl sm:text-3xl text-white mt-0.5">
                            #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            Emissão: {{ $pedido->data_pedido ? $pedido->data_pedido->format('d/m/Y') : $pedido->created_at?->format('d/m/Y') }}
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('public.pedido.pdf', $pedido->token_publico) }}"
                               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/15 px-4 py-2 rounded-xl text-xs font-bold transition-all">
                                <svg class="w-4 h-4 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Baixar PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Faixa de Identificação do Cliente e Status --}}
            <div class="p-6 sm:p-8 bg-gray-50/80 border-b border-gray-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <span class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Preparado Exclusivamente Para</span>
                    <p class="font-display font-extrabold text-lg text-[#0A1128]">{{ $nomeDestinatario }}</p>
                    @if($pedido->clienteFinal?->nome_responsavel && $pedido->clienteFinal?->nome_empresa)
                        <p class="text-xs text-gray-600 mt-0.5">A/C: {{ $pedido->clienteFinal->nome_responsavel }}</p>
                    @endif
                    @if($pedido->clienteFinal?->email)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $pedido->clienteFinal->email }}</p>
                    @endif
                </div>

                <div class="sm:text-right">
                    <span class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">Status da Proposta</span>
                    @php
                        $badgeClass = match($pedido->status) {
                            'Orçamento' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'Aguardando Pagamento' => 'bg-blue-50 text-blue-800 border-blue-200',
                            'Em Andamento' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                            'Concluído' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'Cancelado' => 'bg-red-50 text-red-800 border-red-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-200'
                        };
                    @endphp
                    <span class="px-3.5 py-1.5 inline-flex items-center gap-1.5 text-xs font-extrabold rounded-full border {{ $badgeClass }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ $pedido->status }}
                    </span>
                </div>
            </div>

            {{-- Corpo da Proposta e Tabela de Itens --}}
            <div class="p-6 sm:p-10 space-y-8">
                <div>
                    <span class="text-[10px] font-extrabold text-[#FF7A1A] uppercase tracking-widest block mb-1">Objeto da Proposta</span>
                    <h2 class="font-display font-extrabold text-2xl text-[#0A1128]">{{ $pedido->titulo }}</h2>

                    @if($pedido->descricao)
                        <div class="mt-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap bg-gray-50/80 p-5 rounded-2xl border border-gray-200/70">{{ $pedido->descricao }}</div>
                    @endif
                </div>

                @if($pedido->data_entrega)
                    <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-600/10 text-blue-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-[11px] font-extrabold uppercase tracking-wider text-blue-900">Previsão Estimada de Entrega</span>
                            <span class="text-sm font-bold text-blue-700">{{ $pedido->data_entrega->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Itens do Orçamento --}}
                <div>
                    <h3 class="font-display font-extrabold text-lg text-[#0A1128] mb-4">Detalhamento dos Itens</h3>
                    <div class="overflow-x-auto rounded-2xl border border-gray-200/80">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/90">
                                <tr>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Item / Escopo</th>
                                    <th class="px-5 py-3.5 text-center text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Qtd.</th>
                                    <th class="px-5 py-3.5 text-right text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Valor Unitário</th>
                                    <th class="px-5 py-3.5 text-right text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($pedido->itens as $item)
                                    <tr>
                                        <td class="px-5 py-4 font-bold text-[#0A1128]">{{ $item->nome_item }}</td>
                                        <td class="px-5 py-4 text-center text-gray-600">{{ rtrim(rtrim(number_format($item->quantidade, 2, ',', '.'), '0'), ',') }}</td>
                                        <td class="px-5 py-4 text-right text-gray-600">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                        <td class="px-5 py-4 text-right font-extrabold text-[#0A1128]">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-5 py-4 font-bold text-[#0A1128]">{{ $pedido->titulo }}</td>
                                        <td class="px-5 py-4 text-center text-gray-600">1</td>
                                        <td class="px-5 py-4 text-right text-gray-600">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</td>
                                        <td class="px-5 py-4 text-right font-extrabold text-[#0A1128]">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Resumo do Investimento --}}
                <div class="flex justify-end">
                    <div class="bg-[#0A1128] text-white p-6 rounded-2xl min-w-[280px] sm:min-w-[320px] shadow-md">
                        <div class="flex justify-between items-center text-xs text-gray-400 mb-2">
                            <span class="uppercase tracking-wider font-bold">Subtotal</span>
                            <span>R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-baseline pt-3 border-t border-white/10">
                            <span class="text-sm font-bold text-gray-200">Total do Investimento</span>
                            <span class="font-display font-extrabold text-2xl text-[#FF7A1A]">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Área de Aceite Eletrônico --}}
            @if($pedido->status === 'Orçamento')
                <div class="bg-gray-50 p-6 sm:p-10 border-t border-gray-200/80">
                    <div class="max-w-xl">
                        <span class="inline-block text-[10px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Validação Digital</span>
                        <h3 class="font-display font-extrabold text-xl text-[#0A1128] mb-2">Aprovar Proposta Comercial</h3>
                        <p class="text-gray-600 text-xs sm:text-sm mb-6 leading-relaxed">
                            Para formalizar a aprovação deste orçamento e iniciar o atendimento, informe seu nome completo abaixo. O sistema registrará data, hora e endereço IP como comprovante eletrônico de aceite.
                        </p>

                        <form action="{{ route('public.pedido.approve', $pedido->token_publico) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="nome_aprovacao" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Seu Nome Completo (Responsável pelo Aceite) *
                                </label>
                                <input type="text" name="nome_aprovacao" id="nome_aprovacao" required
                                       placeholder="Digite seu nome completo para assinar"
                                       class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-[#0A1128] focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            </div>
                            <button type="submit" class="w-full sm:w-auto bg-[#FF7A1A] hover:bg-[#E5651A] text-white font-bold py-3.5 px-8 rounded-xl text-sm transition-all shadow-md cursor-pointer">
                                Confirmar e Aprovar Orçamento
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($pedido->nome_aprovacao)
                <div class="bg-emerald-50/90 p-6 sm:p-8 border-t border-emerald-200">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display font-extrabold text-base text-emerald-950">Proposta Aprovada Eletronicamente</h3>
                            <div class="mt-1.5 text-xs text-emerald-800 space-y-0.5">
                                <p><strong>Assinado por:</strong> {{ $pedido->nome_aprovacao }}</p>
                                <p><strong>Data e Hora:</strong> {{ $pedido->data_aprovacao ? $pedido->data_aprovacao->format('d/m/Y \à\s H:i') : 'Registrado' }}</p>
                                <p><strong>IP de Validação:</strong> {{ $pedido->ip_aprovacao ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="text-center text-xs text-gray-400 mt-6 flex items-center justify-center gap-1.5">
            <span>Documento emitido por <strong class="text-gray-600">{{ $nomeEmissor }}</strong></span>
            <span>·</span>
            <span>Tecnologia <strong class="text-gray-600">BruceOS</strong></span>
        </div>
    </div>
</body>
</html>
