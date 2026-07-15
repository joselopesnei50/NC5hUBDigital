<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Minhas Faturas
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Descrição</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Vencimento</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Valor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($faturas as $fatura)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#0A1128]">{{ $fatura->descricao }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ \Carbon\Carbon::parse($fatura->vencimento)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-medium text-[#0A1128]">R$ {{ number_format($fatura->valor, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $fatura->status == 'pendente' ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700' }} uppercase">
                                    {{ $fatura->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($fatura->status == 'pendente')
                                    @if($fatura->link_pagamento)
                                        <a href="{{ $fatura->link_pagamento }}" target="_blank" class="inline-block bg-[#FF7A1A] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#0A1128] transition-colors">
                                            Pagar Agora
                                        </a>
                                    @else
                                        <button class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-bold" disabled>
                                            Processando...
                                        </button>
                                    @endif
                                @else
                                    <button class="text-[#8A8F9C] px-4 py-2 text-sm font-bold" disabled>
                                        Pago
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#8A8F9C]">
                                <p class="font-medium text-[#0A1128]">Nenhuma fatura encontrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
