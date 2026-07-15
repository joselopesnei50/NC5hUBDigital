<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                Aprovação de Materiais
            </h2>
            <a href="{{ route('admin.materiais.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Novo Material
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
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Material</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Cliente</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Data</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($materiais as $material)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#0A1128]">{{ $material->titulo }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $material->cliente->razao_social ?? 'Cliente Deletado' }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $material->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $material->status_aprovacao == 'aprovado' ? 'bg-emerald-100 text-emerald-700' : ($material->status_aprovacao == 'reprovado' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }} uppercase">
                                    {{ str_replace('_', ' ', $material->status_aprovacao) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.materiais.edit', $material->id) }}" class="text-[#8A8F9C] hover:text-[#FF7A1A] font-semibold text-sm transition-colors">Editar</a>
                                    <a href="{{ route('admin.materiais.show', $material->id) }}" class="text-[#0A1128] hover:text-[#FF7A1A] font-bold text-sm transition-colors">Abrir</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#8A8F9C]">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="font-medium text-[#0A1128]">Nenhum material na esteira.</p>
                                <p class="text-sm mt-1">Envie o primeiro layout ou criativo para um cliente aprovar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $materiais->links() }}
        </div>
    </div>
</x-admin-layout>
