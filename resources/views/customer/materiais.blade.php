<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Aprovação de Materiais
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Material / Campanha</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Data de Envio</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($materiais as $material)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#0A1128]">{{ $material->titulo }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $material->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $material->status_aprovacao == 'aprovado' ? 'bg-emerald-100 text-emerald-700' : ($material->status_aprovacao == 'reprovado' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }} uppercase">
                                    {{ str_replace('_', ' ', $material->status_aprovacao) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ $material->arquivo_path }}" target="_blank" class="bg-[#0A1128] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#FF7A1A] transition-colors">
                                    Ver e Avaliar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-[#8A8F9C]">
                                <p class="font-medium text-[#0A1128]">Nenhum material pendente.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
