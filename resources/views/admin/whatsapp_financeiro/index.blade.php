<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    WhatsApp Bot — Financeiro
                </h2>
                <p class="text-sm text-gray-500 mt-1">Consumo DeepSeek por cliente. {{ $periodoLabel }}.</p>
            </div>
            <div class="inline-flex rounded-xl bg-white border border-gray-200 p-1 text-sm">
                <a href="{{ route('admin.whatsapp-financeiro.index', ['periodo' => '30d']) }}"
                   class="px-3 py-1.5 rounded-lg font-bold {{ $periodo === '30d' ? 'bg-[#0A1128] text-white' : 'text-gray-600 hover:text-gray-900' }}">30 dias</a>
                <a href="{{ route('admin.whatsapp-financeiro.index', ['periodo' => 'mes_atual']) }}"
                   class="px-3 py-1.5 rounded-lg font-bold {{ $periodo === 'mes_atual' ? 'bg-[#0A1128] text-white' : 'text-gray-600 hover:text-gray-900' }}">Mês atual</a>
                <a href="{{ route('admin.whatsapp-financeiro.index', ['periodo' => 'mes_anterior']) }}"
                   class="px-3 py-1.5 rounded-lg font-bold {{ $periodo === 'mes_anterior' ? 'bg-[#0A1128] text-white' : 'text-gray-600 hover:text-gray-900' }}">Mês anterior</a>
            </div>
        </div>
    </x-slot>

    {{-- Totais --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Clientes ativos</p>
            <p class="font-bold text-3xl text-gray-900 mt-2">{{ number_format($totais['clientes_ativos'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Mensagens IA</p>
            <p class="font-bold text-3xl text-gray-900 mt-2">{{ number_format($totais['msgs'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Custo total DeepSeek</p>
            <p class="font-bold text-3xl text-emerald-700 mt-2">US$ {{ number_format($totais['cost_total'], 4, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Tokens totais</p>
            <p class="font-bold text-3xl text-gray-900 mt-2">{{ number_format($totais['tokens_total'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
        @if($linhas->isEmpty())
            <div class="p-8 text-center text-gray-500 text-sm">
                Nenhuma interação do bot no período.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Cliente</th>
                            <th class="px-4 py-3 text-right font-bold">Msgs</th>
                            <th class="px-4 py-3 text-right font-bold">Tokens</th>
                            <th class="px-4 py-3 text-right font-bold">Custo total</th>
                            <th class="px-4 py-3 text-right font-bold">Custo médio</th>
                            <th class="px-4 py-3 text-right font-bold">Taxa de erro</th>
                            <th class="px-4 py-3 text-right font-bold">Última atividade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($linhas as $l)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-900">{{ $l['razao_social'] }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($l['msgs'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($l['tokens_total'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-700">US$ {{ number_format($l['cost_total'], 4, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">US$ {{ number_format($l['cost_medio'], 6, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right {{ $l['taxa_erro'] > 10 ? 'text-rose-700 font-bold' : 'text-gray-700' }}">{{ number_format($l['taxa_erro'], 1, ',', '.') }}%</td>
                                <td class="px-4 py-3 text-right text-gray-500 text-xs">{{ $l['ultima_atividade']?->diffForHumans() ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-admin-layout>
