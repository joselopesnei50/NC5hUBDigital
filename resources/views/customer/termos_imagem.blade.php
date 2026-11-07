<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                    Termos de Autorização de Uso de Imagem e Voz
                </h2>
                <p class="text-sm text-[#8A8F9C] mt-1">
                    Consulte os termos de autorização de imagem e voz vinculados às produções da sua empresa.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Título / Projeto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Cedente</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Data</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($termos as $termo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-[#0A1128]">{{ $termo->titulo }}</div>
                                @if($termo->projeto_referencia)
                                    <div class="text-xs text-[#8A8F9C]">{{ $termo->projeto_referencia }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-[#0A1128]">
                                    {{ $termo->cedente_nome }}
                                    @if($termo->cedente_menor)
                                        <span class="ml-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700 uppercase">Menor</span>
                                    @endif
                                </div>
                                @if($termo->cedente_menor && $termo->responsavel_nome)
                                    <div class="text-xs text-[#8A8F9C]">Resp.: {{ $termo->responsavel_nome }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-[#8A8F9C]">
                                @if($termo->assinado_em)
                                    Assinado em {{ $termo->assinado_em->format('d/m/Y H:i') }}
                                @elseif($termo->data_captacao)
                                    Captação: {{ $termo->data_captacao->format('d/m/Y') }}
                                @else
                                    Emitido em {{ $termo->created_at->format('d/m/Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = [
                                        'rascunho'  => 'bg-gray-100 text-gray-700',
                                        'enviado'   => 'bg-amber-100 text-amber-700',
                                        'assinado'  => 'bg-emerald-100 text-emerald-700',
                                        'cancelado' => 'bg-rose-100 text-rose-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $colors[$termo->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $termo->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($termo->status === 'assinado')
                                    <a href="{{ route('customer.termos-imagem.pdf', $termo->id) }}"
                                       class="inline-flex items-center gap-1.5 bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Baixar PDF
                                    </a>
                                @else
                                    <span class="text-xs text-[#8A8F9C]">Aguardando assinatura</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <p class="font-medium text-[#0A1128]">Nenhum termo de uso de imagem vinculado à sua conta.</p>
                                <p class="text-xs text-[#8A8F9C] mt-1">Quando a equipe NC5 registrar termos para suas gravações, eles aparecerão aqui.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
