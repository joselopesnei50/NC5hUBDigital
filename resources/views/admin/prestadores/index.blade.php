<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Prestadores de Serviço</h2>
                <p class="text-sm text-[#8A8F9C] mt-1">Freelancers e parceiros contratados pela NC5.</p>
            </div>
            <a href="{{ route('admin.prestadores.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg flex items-center gap-2 w-fit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo Prestador
            </a>
        </div>
    </x-slot>

    <form method="GET" class="mb-6 flex gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome, CPF/CNPJ, e-mail ou categoria..."
               class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        <button type="submit" class="bg-white border border-gray-300 hover:border-[#0A1128] text-[#0A1128] px-5 py-2 rounded-xl text-sm font-bold transition-colors">Buscar</button>
        @if($search)
            <a href="{{ route('admin.prestadores.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-semibold text-sm self-center">Limpar</a>
        @endif
    </form>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Nome</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Tipo</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Categoria</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Contato</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($prestadores as $prestador)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-[#0A1128]">{{ $prestador->nome }}</div>
                                @if($prestador->cpf_cnpj)
                                    <div class="text-xs text-[#8A8F9C]">{{ $prestador->cpf_cnpj }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $prestador->tipo_pessoa }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $prestador->categoria ?: '—' }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">
                                <div>{{ $prestador->email ?: '—' }}</div>
                                <div class="text-xs">{{ $prestador->telefone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $prestador->ativo ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $prestador->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.contratos-prestadores.create', ['prestador_id' => $prestador->id]) }}" class="text-[#FF7A1A] hover:underline font-semibold text-sm">Novo contrato</a>
                                    <a href="{{ route('admin.prestadores.show', $prestador) }}" class="text-[#0A1128] hover:text-[#FF7A1A] font-bold text-sm transition-colors">Abrir</a>
                                    <a href="{{ route('admin.prestadores.edit', $prestador) }}" class="text-[#8A8F9C] hover:text-[#FF7A1A] font-semibold text-sm transition-colors">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-medium text-[#0A1128]">Nenhum prestador cadastrado.</p>
                                <a href="{{ route('admin.prestadores.create') }}" class="inline-block mt-3 text-[#FF7A1A] font-bold text-sm hover:underline">Cadastrar o primeiro</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $prestadores->links() }}
        </div>
    </div>
</x-admin-layout>
