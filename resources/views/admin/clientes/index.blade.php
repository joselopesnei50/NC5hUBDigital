<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                Gestão de Clientes
            </h2>
            <a href="{{ route('admin.clientes.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo Cliente
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
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Cliente</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Documento</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Cadastro</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($clientes as $cliente)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-[#0A1128]">{{ $cliente->razao_social }}</p>
                                <p class="text-sm text-[#8A8F9C]">{{ $cliente->user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#0A1128] font-medium">
                                <span class="px-2 py-1 bg-[#F4F5F7] text-[#8A8F9C] text-xs font-bold rounded">{{ $cliente->tipo_pessoa }}</span> 
                                {{ $cliente->cpf_cnpj }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700 uppercase">
                                    {{ $cliente->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">
                                {{ $cliente->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="text-[#8A8F9C] hover:text-[#FF7A1A] font-semibold text-sm transition-colors">Editar</a>
                                    <a href="{{ route('admin.clientes.show', $cliente->id) }}" class="text-[#0A1128] hover:text-[#FF7A1A] font-bold text-sm transition-colors">Abrir</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <p class="font-medium text-gray-600">Nenhum cliente cadastrado.</p>
                                <p class="text-sm mt-1">Comece criando um novo cliente isolado no sistema.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $clientes->links() }}
        </div>
    </div>
</x-admin-layout>
