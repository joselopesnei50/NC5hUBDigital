<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Compras</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Fornecedores</h2>
            <p class="text-slate text-sm mt-1">Cadastre quem fornece serviços e produtos pra sua empresa. Aparecem na hora de lançar contas a pagar.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <form method="GET" action="{{ route('customer.fornecedores.index') }}" class="flex-1 flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nome, CNPJ, e-mail ou categoria..."
                           class="flex-1 rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    <button type="submit" class="px-4 py-2 bg-ink hover:bg-bruce text-white rounded-xl text-sm font-bold transition-colors">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('customer.fornecedores.index') }}" class="px-4 py-2 bg-mist text-slate hover:text-ink rounded-xl text-sm font-bold transition-colors">
                            Limpar
                        </a>
                    @endif
                </form>

                <a href="{{ route('customer.fornecedores.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-bruce hover:bg-bruceDark text-white rounded-xl text-sm font-bold transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Novo fornecedor
                </a>
            </div>

            @if($fornecedores->isEmpty())
                <div class="p-10 rounded-2xl bg-mist text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-white text-slate flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <p class="font-bold text-ink">{{ $search ? 'Nenhum fornecedor encontrado.' : 'Você ainda não tem fornecedores cadastrados.' }}</p>
                    <p class="text-sm text-slate mt-1">Cadastre agora pra usar depois nos lançamentos de contas a pagar.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-black/5 text-left">
                                <th class="pb-3 pr-4 text-[11px] font-bold uppercase tracking-wider text-slate">Nome</th>
                                <th class="pb-3 pr-4 text-[11px] font-bold uppercase tracking-wider text-slate">CNPJ/CPF</th>
                                <th class="pb-3 pr-4 text-[11px] font-bold uppercase tracking-wider text-slate">Contato</th>
                                <th class="pb-3 pr-4 text-[11px] font-bold uppercase tracking-wider text-slate">Categoria</th>
                                <th class="pb-3 pr-4 text-[11px] font-bold uppercase tracking-wider text-slate text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach($fornecedores as $f)
                                <tr>
                                    <td class="py-3 pr-4">
                                        <p class="font-bold text-ink">{{ $f->nome }}</p>
                                        @if($f->endereco)
                                            <p class="text-[11px] text-slate">{{ $f->endereco }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4 font-mono text-xs text-slate">{{ $f->cnpj_cpf ?: '—' }}</td>
                                    <td class="py-3 pr-4">
                                        @if($f->email)
                                            <p class="text-ink">{{ $f->email }}</p>
                                        @endif
                                        @if($f->telefone)
                                            <p class="text-[11px] text-slate">{{ $f->telefone }}</p>
                                        @endif
                                        @if(!$f->email && !$f->telefone)
                                            <span class="text-slate">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4">
                                        @if($f->categoria)
                                            <span class="inline-flex items-center bg-bruce/10 text-bruce text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">{{ $f->categoria }}</span>
                                        @else
                                            <span class="text-slate">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4 text-right whitespace-nowrap">
                                        <a href="{{ route('customer.fornecedores.edit', $f) }}"
                                           class="text-xs font-bold text-ink hover:text-bruce mr-3">Editar</a>
                                        <form action="{{ route('customer.fornecedores.destroy', $f) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Remover este fornecedor? Os lançamentos anteriores continuam com o nome atual.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $fornecedores->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
