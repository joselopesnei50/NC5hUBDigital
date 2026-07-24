<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display font-extrabold text-2xl text-ink leading-tight tracking-tight">
                    Hub de Produção & Materiais
                </h2>
                <p class="text-slateText text-sm mt-1">Gerencie a esteira de aprovação de materiais, peças e criativos junto aos clientes.</p>
            </div>
            <a href="{{ route('admin.materiais.create') }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-6 py-3 rounded-2xl text-sm font-bold transition-all shadow-md flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Enviar Novo Material
            </a>
        </div>
    </x-slot>

    <!-- Stat Cards Header -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="glass-panel p-5 rounded-2xl shadow-premium border-l-4 border-l-amber-500">
            <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Pendente de Aprovação</span>
            <span class="text-2xl font-display font-extrabold text-ink mt-1 block">
                {{ \App\Models\Material::where('status_aprovacao', 'pendente')->count() }}
            </span>
        </div>
        <div class="glass-panel p-5 rounded-2xl shadow-premium border-l-4 border-l-purple-500">
            <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Ajustes Solicitados</span>
            <span class="text-2xl font-display font-extrabold text-purple-700 mt-1 block">
                {{ \App\Models\Material::where('status_aprovacao', 'ajustes_solicitados')->count() }}
            </span>
        </div>
        <div class="glass-panel p-5 rounded-2xl shadow-premium border-l-4 border-l-emerald-500">
            <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Aprovados</span>
            <span class="text-2xl font-display font-extrabold text-emerald-700 mt-1 block">
                {{ \App\Models\Material::where('status_aprovacao', 'aprovado')->count() }}
            </span>
        </div>
        <div class="glass-panel p-5 rounded-2xl shadow-premium border-l-4 border-l-rose-500">
            <span class="text-xs font-bold text-slateText uppercase tracking-wider block">Reprovados</span>
            <span class="text-2xl font-display font-extrabold text-rose-700 mt-1 block">
                {{ \App\Models\Material::where('status_aprovacao', 'reprovado')->count() }}
            </span>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-3xl overflow-hidden shadow-premium">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-extrabold text-slateText uppercase tracking-wider">Material / Peça</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slateText uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slateText uppercase tracking-wider">Data de Envio</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slateText uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slateText uppercase tracking-wider">Observações do Cliente</th>
                        <th class="px-6 py-4 text-xs font-extrabold text-slateText uppercase tracking-wider text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($materiais as $material)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-ink text-base">{{ $material->titulo }}</div>
                                @if($material->tipo)
                                    <span class="inline-block mt-0.5 text-xs font-semibold text-slateText bg-slate-100 px-2 py-0.5 rounded-md">
                                        {{ $material->tipo }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-ink text-sm">{{ $material->cliente->razao_social ?? 'Cliente Deletado' }}</div>
                                <div class="text-xs text-slateText">{{ $material->cliente->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slateText font-medium">
                                {{ $material->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($material->status_aprovacao === 'aprovado')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1.5 uppercase">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aprovado
                                    </span>
                                @elseif($material->status_aprovacao === 'ajustes_solicitados')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-200 inline-flex items-center gap-1.5 uppercase">
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Ajustes Solicitados
                                    </span>
                                @elseif($material->status_aprovacao === 'reprovado')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center gap-1.5 uppercase">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Reprovado
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center gap-1.5 uppercase">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Aguardando Cliente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($material->comentario_cliente)
                                    <p class="text-xs text-slate-700 italic max-w-xs truncate" title="{{ $material->comentario_cliente }}">
                                        "{{ $material->comentario_cliente }}"
                                    </p>
                                    @if($material->data_resposta)
                                        <span class="text-[10px] text-slateText block mt-0.5 font-medium">Respondido em {{ $material->data_resposta->format('d/m/Y H:i') }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 italic">Sem observações ainda</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.materiais.show', $material->id) }}" class="bg-ink hover:bg-magenta text-white px-3.5 py-1.5 rounded-xl font-bold text-xs transition-colors shadow-sm">
                                        Detalhes / Ficha
                                    </a>
                                    <a href="{{ route('admin.materiais.edit', $material->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl font-semibold text-xs transition-colors">
                                        Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slateText">
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <h3 class="font-display font-bold text-lg text-ink">Nenhum material na esteira de produção.</h3>
                                <p class="text-sm text-slateText mt-1">Clique acima em "Enviar Novo Material" para iniciar uma validação com o cliente.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $materiais->links() }}
        </div>
    </div>
</x-admin-layout>
