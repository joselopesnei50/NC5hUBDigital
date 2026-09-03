<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Minhas Faturas
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 font-medium text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase tracking-wider">Descrição</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase tracking-wider">Vencimento</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase tracking-wider">Valor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase tracking-wider">Forma Pgto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase tracking-wider text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($faturas as $fatura)
                        @php
                            $formaLabels = [
                                'pix' => 'PIX',
                                'boleto' => 'Boleto',
                                'transferencia' => 'Transferência',
                                'link_pagamento' => 'Link',
                            ];
                            $formaPgtoFormatada = $fatura->forma_pagamento ? ($formaLabels[$fatura->forma_pagamento] ?? ucfirst(str_replace('_', ' ', $fatura->forma_pagamento))) : '—';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#0A1128]">
                                {{ $fatura->descricao ?? 'Fatura #' . $fatura->id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">
                                {{ \Carbon\Carbon::parse($fatura->vencimento)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-[#0A1128]">
                                R$ {{ number_format($fatura->valor, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#0A1128]">
                                {{ $formaPgtoFormatada }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase
                                        @if($fatura->status == 'pago') bg-emerald-100 text-emerald-700
                                        @elseif($fatura->status == 'atrasado') bg-red-100 text-red-700
                                        @elseif($fatura->status == 'cancelado') bg-gray-200 text-gray-700
                                        @else bg-orange-100 text-orange-700 @endif">
                                        {{ $fatura->status }}
                                    </span>
                                    @if($fatura->comprovante_path)
                                        <span title="Comprovante enviado" class="inline-flex items-center text-emerald-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('customer.invoices.show', $fatura->id) }}" class="text-[#0A1128] hover:text-[#FF7A1A] font-bold text-sm transition-colors">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[#8A8F9C]">
                                <p class="font-medium text-[#0A1128]">Nenhuma fatura encontrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
