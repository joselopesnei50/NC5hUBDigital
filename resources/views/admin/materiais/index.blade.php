<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display font-extrabold text-2xl text-ink leading-tight tracking-tight">
                    Hub de Produção &amp; Materiais
                </h2>
                <p class="text-slateText text-sm mt-1">Gerencie a esteira de aprovação de materiais, peças e criativos junto aos clientes.</p>
            </div>
            <a href="{{ route('admin.materiais.create') }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-md flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Enviar Novo Material</span>
            </a>
        </div>
    </x-slot>

    <!-- Stat Cards Header (Filtros Clicáveis Rápidos) -->
    @php
        $countPendente = \App\Models\Material::where('status_aprovacao', 'pendente')->count();
        $countAjustes = \App\Models\Material::where('status_aprovacao', 'ajustes_solicitados')->count();
        $countAprovados = \App\Models\Material::where('status_aprovacao', 'aprovado')->count();
        $countReprovados = \App\Models\Material::where('status_aprovacao', 'reprovado')->count();
        $currStatus = $status ?? request('status');
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 mb-6">
        <a href="{{ route('admin.materiais.index', array_merge(request()->except('page'), ['status' => $currStatus === 'pendente' ? null : 'pendente'])) }}"
           class="glass-panel p-4 sm:p-5 rounded-2xl shadow-premium border-l-4 transition hover:bg-slate-50/80 {{ $currStatus === 'pendente' ? 'border-l-amber-500 ring-2 ring-amber-400/30 bg-amber-50/20' : 'border-l-amber-500' }}">
            <span class="text-[11px] font-bold text-slateText uppercase tracking-wider block">Pendente de Aprovação</span>
            <div class="flex items-center justify-between mt-1">
                <span class="text-2xl font-display font-extrabold text-amber-700">{{ $countPendente }}</span>
                @if($currStatus === 'pendente')
                    <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded">Ativo</span>
                @endif
            </div>
        </a>

        <a href="{{ route('admin.materiais.index', array_merge(request()->except('page'), ['status' => $currStatus === 'ajustes_solicitados' ? null : 'ajustes_solicitados'])) }}"
           class="glass-panel p-4 sm:p-5 rounded-2xl shadow-premium border-l-4 transition hover:bg-slate-50/80 {{ $currStatus === 'ajustes_solicitados' ? 'border-l-purple-500 ring-2 ring-purple-400/30 bg-purple-50/20' : 'border-l-purple-500' }}">
            <span class="text-[11px] font-bold text-slateText uppercase tracking-wider block">Ajustes Solicitados</span>
            <div class="flex items-center justify-between mt-1">
                <span class="text-2xl font-display font-extrabold text-purple-700">{{ $countAjustes }}</span>
                @if($currStatus === 'ajustes_solicitados')
                    <span class="text-[10px] font-bold text-purple-800 bg-purple-100 px-1.5 py-0.5 rounded">Ativo</span>
                @endif
            </div>
        </a>

        <a href="{{ route('admin.materiais.index', array_merge(request()->except('page'), ['status' => $currStatus === 'aprovado' ? null : 'aprovado'])) }}"
           class="glass-panel p-4 sm:p-5 rounded-2xl shadow-premium border-l-4 transition hover:bg-slate-50/80 {{ $currStatus === 'aprovado' ? 'border-l-emerald-500 ring-2 ring-emerald-400/30 bg-emerald-50/20' : 'border-l-emerald-500' }}">
            <span class="text-[11px] font-bold text-slateText uppercase tracking-wider block">Aprovados</span>
            <div class="flex items-center justify-between mt-1">
                <span class="text-2xl font-display font-extrabold text-emerald-700">{{ $countAprovados }}</span>
                @if($currStatus === 'aprovado')
                    <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-1.5 py-0.5 rounded">Ativo</span>
                @endif
            </div>
        </a>

        <a href="{{ route('admin.materiais.index', array_merge(request()->except('page'), ['status' => $currStatus === 'reprovado' ? null : 'reprovado'])) }}"
           class="glass-panel p-4 sm:p-5 rounded-2xl shadow-premium border-l-4 transition hover:bg-slate-50/80 {{ $currStatus === 'reprovado' ? 'border-l-rose-500 ring-2 ring-rose-400/30 bg-rose-50/20' : 'border-l-rose-500' }}">
            <span class="text-[11px] font-bold text-slateText uppercase tracking-wider block">Reprovados</span>
            <div class="flex items-center justify-between mt-1">
                <span class="text-2xl font-display font-extrabold text-rose-700">{{ $countReprovados }}</span>
                @if($currStatus === 'reprovado')
                    <span class="text-[10px] font-bold text-rose-800 bg-rose-100 px-1.5 py-0.5 rounded">Ativo</span>
                @endif
            </div>
        </a>
    </div>

    <!-- Barra de Busca & Filtros -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 mb-5 shadow-2xs">
        <form method="GET" action="{{ route('admin.materiais.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:max-w-md">
                <input type="text"
                       name="search"
                       value="{{ $search ?? request('search') }}"
                       placeholder="Buscar por peça, tipo ou cliente..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-bruce focus:ring-bruce">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                @if(!empty($currStatus))
                    <input type="hidden" name="status" value="{{ $currStatus }}">
                @endif

                <button type="submit" class="px-4 py-2 rounded-xl bg-ink hover:bg-bruce text-white text-xs font-bold transition shadow-2xs">
                    Filtrar
                </button>

                @if(!empty($search) || !empty($currStatus))
                    <a href="{{ route('admin.materiais.index') }}"
                       class="px-3 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold transition"
                       title="Remover filtros">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Main Table Card (Otimizado sem barra de rolagem forçada + Ações Fixadas) -->
    <div class="bg-white border border-slate-200/80 rounded-3xl overflow-hidden shadow-premium relative">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed min-w-[760px] md:min-w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="w-[28%] px-4 sm:px-5 py-3.5 text-xs font-extrabold text-slateText uppercase tracking-wider">Material / Peça</th>
                        <th class="w-[22%] px-4 sm:px-5 py-3.5 text-xs font-extrabold text-slateText uppercase tracking-wider">Cliente</th>
                        <th class="w-[18%] px-4 sm:px-5 py-3.5 text-xs font-extrabold text-slateText uppercase tracking-wider">Status</th>
                        <th class="w-[18%] px-4 sm:px-5 py-3.5 text-xs font-extrabold text-slateText uppercase tracking-wider">Feedback do Cliente</th>
                        <th class="w-[14%] px-4 sm:px-5 py-3.5 text-xs font-extrabold text-slateText uppercase tracking-wider text-right sticky right-0 bg-slate-50 shadow-[-6px_0_10px_-2px_rgba(0,0,0,0.04)] z-10">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($materiais as $material)
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            {{-- Material & Envio --}}
                            <td class="px-4 sm:px-5 py-3.5 truncate">
                                <div class="font-bold text-ink text-sm sm:text-base truncate" title="{{ $material->titulo }}">
                                    {{ $material->titulo }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    @if($material->tipo)
                                        <span class="inline-block text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.2 rounded">
                                            {{ $material->tipo }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] text-slate-400">
                                        &bull; {{ $material->created_at->format('d/m/y H:i') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Cliente --}}
                            <td class="px-4 sm:px-5 py-3.5 truncate">
                                <div class="font-semibold text-ink text-xs sm:text-sm truncate" title="{{ $material->cliente->razao_social ?? 'Cliente Deletado' }}">
                                    {{ $material->cliente->razao_social ?? 'Cliente Deletado' }}
                                </div>
                                <div class="text-[11px] text-slateText truncate">
                                    {{ $material->cliente->user->email ?? '' }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 sm:px-5 py-3.5 whitespace-nowrap">
                                @if($material->status_aprovacao === 'aprovado')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1.5 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aprovado
                                    </span>
                                @elseif($material->status_aprovacao === 'ajustes_solicitados')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-purple-50 text-purple-800 border border-purple-200 inline-flex items-center gap-1.5 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Ajustes Solicitados
                                    </span>
                                @elseif($material->status_aprovacao === 'reprovado')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-rose-50 text-rose-800 border border-rose-200 inline-flex items-center gap-1.5 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Reprovado
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-200 inline-flex items-center gap-1.5 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Aguardando
                                    </span>
                                @endif
                            </td>

                            {{-- Feedback / Comentário --}}
                            <td class="px-4 sm:px-5 py-3.5">
                                @if($material->comentario_cliente)
                                    <p class="text-xs text-slate-700 italic truncate" title="{{ $material->comentario_cliente }}">
                                        "{{ $material->comentario_cliente }}"
                                    </p>
                                    @if($material->data_resposta)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">
                                            {{ $material->data_resposta->format('d/m H:i') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 italic">Sem observações</span>
                                @endif
                            </td>

                            {{-- Ações (Sticky na direita: NUNCA corta nem exige rolagem lateral) --}}
                            <td class="px-4 sm:px-5 py-3.5 text-right sticky right-0 bg-white group-hover:bg-slate-50/70 transition-colors shadow-[-6px_0_10px_-2px_rgba(0,0,0,0.04)] z-10 whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.materiais.show', $material->id) }}"
                                       title="Ver Ficha / Detalhes"
                                       class="inline-flex items-center gap-1 bg-ink hover:bg-bruce text-white px-2.5 py-1.5 rounded-xl font-bold text-xs transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span class="hidden sm:inline">Ficha</span>
                                    </a>

                                    <a href="{{ route('admin.materiais.edit', $material->id) }}"
                                       title="Editar Material"
                                       class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1.5 rounded-xl font-semibold text-xs transition-colors">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span class="hidden sm:inline">Editar</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slateText">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <h3 class="font-display font-bold text-base text-ink">Nenhum material encontrado.</h3>
                                <p class="text-xs text-slateText mt-1">Nenhum item corresponde aos filtros selecionados.</p>
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
