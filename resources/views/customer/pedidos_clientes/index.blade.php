<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Operação Comercial · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">Gestão de Pedidos & Orçamentos</h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                    <a href="{{ route('customer.pedidos.index') }}" class="px-4 py-2 text-xs font-bold rounded-lg bg-[#0A1128] text-white transition-all">
                        Lista
                    </a>
                    <a href="{{ route('customer.pedidos.kanban') }}" class="px-4 py-2 text-xs font-bold rounded-lg text-gray-600 hover:text-[#0A1128] transition-all">
                        Quadro Kanban
                    </a>
                </div>

                <a href="{{ route('customer.pedidos.create') }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Novo Pedido
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            {{-- Barra de Busca --}}
            <div class="p-6 border-b border-gray-100">
                <form method="GET" action="{{ route('customer.pedidos.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por título, descrição ou cliente..."
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-[#0A1128] hover:bg-[#141D38] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors">
                            Buscar
                        </button>
                        @if($search)
                            <a href="{{ route('customer.pedidos.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabela --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                            <th class="px-6 py-4">Pedido</th>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4">Valor</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Entrega Prev.</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($pedidos as $pedido)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-[#0A1128]">{{ $pedido->titulo }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                                        @if($pedido->data_pedido)
                                            · {{ $pedido->data_pedido->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-[#0A1128]">{{ $pedido->clienteFinal->nome_empresa ?? '—' }}</div>
                                    @if(!empty($pedido->clienteFinal->nome_responsavel))
                                        <div class="text-xs text-gray-500">{{ $pedido->clienteFinal->nome_responsavel }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-extrabold text-[#0A1128]">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClass = match($pedido->status) {
                                            'Orçamento'            => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'Aguardando Pagamento' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Em Andamento'         => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'Concluído'            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Cancelado'            => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default                => 'bg-gray-100 text-gray-700 border-gray-200'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full border {{ $badgeClass }}">
                                        {{ $pedido->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-medium">
                                    {{ $pedido->data_entrega ? $pedido->data_entrega->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        @if($pedido->token_publico)
                                            <button type="button" onclick="copiarLink('{{ route('public.pedido.show', $pedido->token_publico) }}')"
                                                    class="p-2 rounded-lg text-gray-500 hover:text-[#FF7A1A] hover:bg-orange-50 transition-colors" title="Copiar Link da Proposta">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            </button>
                                        @endif
                                        <a href="{{ route('customer.pedidos.pdf', $pedido->id) }}"
                                           class="p-2 rounded-lg text-gray-500 hover:text-[#0A1128] hover:bg-gray-100 transition-colors" title="Baixar PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                        <a href="{{ route('customer.pedidos.edit', $pedido->id) }}"
                                           class="p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('customer.pedidos.destroy', $pedido->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este pedido?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Excluir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center text-gray-400 font-medium">
                                    Nenhum pedido registrado até o momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pedidos->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function copiarLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                alert('Link público da proposta copiado para a área de transferência!');
            }).catch(err => {
                console.error('Erro ao copiar: ', err);
            });
        }
    </script>
</x-app-layout>
