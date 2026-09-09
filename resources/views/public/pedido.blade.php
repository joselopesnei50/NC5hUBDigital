<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proposta #{{ $pedido->id }} - {{ $pedido->cliente->nome_empresa }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-100 py-10 px-4">
    <div class="max-w-3xl mx-auto">
        
        <!-- Alertas -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Principal -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <!-- Cabeçalho -->
            <div class="bg-slate-800 text-white p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold">{{ $pedido->cliente->razao_social }}</h1>
                        <p class="text-slate-300 mt-1">CNPJ/Doc: {{ $pedido->cliente->cpf_cnpj ?? 'Não informado' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold uppercase tracking-wider text-slate-400">Proposta / Pedido</div>
                        <div class="text-2xl font-bold mt-1">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-sm text-slate-300 mt-1">Data: {{ $pedido->data_pedido ? $pedido->data_pedido->format('d/m/Y') : $pedido->created_at->format('d/m/Y') }}</div>
                        <div class="mt-4">
                            <a href="{{ route('public.pedido.pdf', $pedido->token_publico) }}" class="inline-flex items-center text-sm bg-slate-700 hover:bg-slate-600 px-3 py-2 rounded transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Baixar PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados do Cliente -->
            <div class="p-8 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between gap-6">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Preparado Para</h3>
                    <p class="font-bold text-gray-800 text-lg">{{ $pedido->clienteFinal->nome_empresa }}</p>
                    <p class="text-gray-600">{{ $pedido->clienteFinal->nome_responsavel }}</p>
                    <p class="text-gray-600">{{ $pedido->clienteFinal->email }}</p>
                </div>
                <div class="md:text-right">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Status Atual</h3>
                    @php
                        $badgeClass = match($pedido->status) {
                            'Orçamento' => 'bg-gray-200 text-gray-800',
                            'Aguardando Pagamento' => 'bg-yellow-100 text-yellow-800',
                            'Em Andamento' => 'bg-blue-100 text-blue-800',
                            'Concluído' => 'bg-green-100 text-green-800',
                            'Cancelado' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full {{ $badgeClass }}">
                        {{ $pedido->status }}
                    </span>
                </div>
            </div>

            <!-- Escopo do Pedido -->
            <div class="p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Detalhes do Pedido / Projeto</h2>
                
                <div class="mb-6">
                    <h3 class="font-bold text-lg text-gray-800">{{ $pedido->titulo }}</h3>
                    
                    @if($pedido->descricao)
                        <div class="mt-4 text-gray-600 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-100">{{ $pedido->descricao }}</div>
                    @endif
                </div>

                @if($pedido->data_entrega)
                    <div class="mb-6 bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-center">
                        <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <div>
                            <span class="block text-sm font-semibold text-blue-800">Previsão de Entrega</span>
                            <span class="text-blue-600">{{ $pedido->data_entrega->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Tabela de Itens -->
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Itens Incluídos</h3>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-bold text-gray-600 uppercase tracking-wider">Descrição do Item</th>
                                    <th class="px-6 py-3 text-center font-bold text-gray-600 uppercase tracking-wider">Qtd</th>
                                    <th class="px-6 py-3 text-right font-bold text-gray-600 uppercase tracking-wider">V. Unitário</th>
                                    <th class="px-6 py-3 text-right font-bold text-gray-600 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pedido->itens as $item)
                                    <tr>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nome_item }}</td>
                                        <td class="px-6 py-4 text-center text-gray-700">{{ number_format($item->quantidade, 2, ',', '') }}</td>
                                        <td class="px-6 py-4 text-right text-gray-700">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-800">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total -->
                <div class="mt-6 flex justify-end">
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 min-w-[250px]">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-500 font-medium">Subtotal</span>
                            <span class="text-gray-800 font-semibold">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-300 mt-2">
                            <span class="text-xl font-bold text-gray-800">Total Final</span>
                            <span class="text-2xl font-bold text-green-600">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Área de Aprovação Digital -->
            @if($pedido->status === 'Orçamento')
                <div class="bg-blue-50 p-8 border-t border-blue-100">
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Aceite Eletrônico</h3>
                    <p class="text-blue-700 text-sm mb-6">Para darmos andamento, por favor preencha seu nome abaixo e clique em aprovar. Este aceite registrará o seu IP para fins de segurança.</p>
                    
                    <form action="{{ route('public.pedido.approve', $pedido->token_publico) }}" method="POST" class="max-w-md">
                        @csrf
                        <div class="mb-4">
                            <label for="nome_aprovacao" class="block text-sm font-medium text-blue-900 mb-1">Nome Completo (Assinatura Eletrônica) *</label>
                            <input type="text" name="nome_aprovacao" id="nome_aprovacao" required class="w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Digite seu nome completo">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded hover:bg-blue-700 transition">
                            Aprovar Orçamento
                        </button>
                    </form>
                </div>
            @elseif($pedido->nome_aprovacao)
                <div class="bg-green-50 p-8 border-t border-green-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-green-800">Aprovado Eletronicamente</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p><strong>Por:</strong> {{ $pedido->nome_aprovacao }}</p>
                                <p><strong>Data:</strong> {{ $pedido->data_aprovacao ? $pedido->data_aprovacao->format('d/m/Y \à\s H:i') : 'N/A' }}</p>
                                <p><strong>IP Registrado:</strong> {{ $pedido->ip_aprovacao }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="text-center text-sm text-gray-500 mt-6">
            Documento gerado por {{ $pedido->cliente->razao_social }} via plataforma integrada.
        </div>
    </div>
</body>
</html>
