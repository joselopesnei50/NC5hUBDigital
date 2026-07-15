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

    @if($cc = session('cliente_criado'))
        <div x-data="{ copiado: false }" class="mb-6 relative overflow-hidden bg-[#0A1128] rounded-2xl p-6 lg:p-8 text-white shadow-xl shadow-[#0A1128]/10">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#FF7A1A]/20 rounded-full blur-[80px] pointer-events-none"></div>

            <div class="relative flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-emerald-300">Cliente criado com sucesso</span>
                    </div>
                    <h3 class="text-xl font-bold leading-tight">{{ $cc['nome'] }}</h3>
                    <p class="text-sm text-white/60 mt-1">{{ $cc['email'] }}</p>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-3">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/50 mb-1">Senha temporária</p>
                            <div class="flex items-center gap-2">
                                <p class="font-mono text-lg font-bold tracking-wider text-white select-all">{{ $cc['senha'] }}</p>
                                <button type="button"
                                        @click="navigator.clipboard.writeText('{{ $cc['senha'] }}'); copiado = true; setTimeout(() => copiado = false, 2000)"
                                        class="ml-auto text-xs font-bold text-[#FF7A1A] hover:text-white transition-colors">
                                    <span x-show="!copiado">Copiar</span>
                                    <span x-show="copiado" style="display:none">Copiado ✓</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-3">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/50 mb-1">E-mail de boas-vindas</p>
                            @if($cc['email_enviado'])
                                <p class="text-sm font-semibold text-emerald-300 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Enviado ao cliente
                                </p>
                            @else
                                <p class="text-sm font-semibold text-[#FF7A1A] flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M4.062 19h15.876c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L2.33 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Falha no envio — passe a senha manualmente
                                </p>
                            @endif
                        </div>
                    </div>

                    @if(!$cc['email_enviado'])
                        <p class="mt-3 text-xs text-white/60">Confira em <span class="font-semibold text-white">Configurações → Envio de e-mails</span> se as credenciais do Brevo estão preenchidas e o domínio autenticado.</p>
                    @endif
                </div>
            </div>
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
