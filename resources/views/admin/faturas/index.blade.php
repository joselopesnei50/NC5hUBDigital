<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                Gestão de Faturas
            </h2>
            <a href="{{ route('admin.faturas.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nova Fatura
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Referência</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Cliente</th>
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
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $fatura->cliente->razao_social ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ \Carbon\Carbon::parse($fatura->vencimento)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-medium text-[#0A1128]">R$ {{ number_format($fatura->valor, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase
                                    {{ $fatura->status == 'pago' ? 'bg-emerald-100 text-emerald-700' : ($fatura->status == 'atrasado' ? 'bg-red-100 text-red-700' : ($fatura->status == 'cancelado' ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-700')) }}">
                                    {{ $fatura->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.faturas.edit', $fatura->id) }}" class="text-[#8A8F9C] hover:text-[#FF7A1A] font-semibold text-sm transition-colors">Editar</a>
                                    <a href="{{ route('admin.faturas.show', $fatura->id) }}" class="text-[#0A1128] hover:text-[#FF7A1A] font-bold text-sm transition-colors">Abrir</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[#8A8F9C]">
                                <p class="font-medium text-[#0A1128]">Nenhuma fatura emitida.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $faturas->links() }}
        </div>
    </div>
</x-admin-layout>
