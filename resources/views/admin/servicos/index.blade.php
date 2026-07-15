<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                Catálogo de Serviços
            </h2>
            <a href="{{ route('admin.servicos.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo Serviço
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
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Nome do Serviço</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Valor Base</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($servicos as $servico)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#0A1128]">{{ $servico->nome }}</td>
                            <td class="px-6 py-4 font-medium text-[#0A1128]">R$ {{ number_format($servico->preco, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $servico->status == 'ativo' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }} uppercase">
                                    {{ $servico->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.servicos.edit', $servico->id) }}" class="text-[#0A1128] hover:text-[#FF7A1A] font-bold text-sm transition-colors">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-[#8A8F9C]">
                                <p class="font-medium text-[#0A1128]">Catálogo vazio.</p>
                                <p class="text-sm mt-1">Cadastre os pacotes que a sua agência vende.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
