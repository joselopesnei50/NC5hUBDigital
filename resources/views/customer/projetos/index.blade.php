<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest mb-2">Operacional</p>
                <h2 class="font-display font-bold text-3xl text-[#0A1128] leading-tight">Meus Projetos e Entregas</h2>
            </div>
            <a href="{{ route('customer.projetos.create') }}" class="px-4 py-2 bg-[#FF7A1A] text-white text-sm font-bold rounded-lg hover:bg-orange-600 transition shadow-lg">
                + Solicitar Projeto
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($projetos as $projeto)
                @php
                    $total = $projeto->tarefas->count();
                    $concluidas = $projeto->tarefas->where('concluida', true)->count();
                    $progresso = $total > 0 ? round(($concluidas / $total) * 100) : 0;
                @endphp
                <a href="{{ route('customer.projetos.show', $projeto) }}" class="block bg-white rounded-3xl p-6 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:-translate-y-1 transition-all border border-slate-100 group relative overflow-hidden">
                    <!-- Progress background -->
                    <div class="absolute bottom-0 left-0 h-1.5 bg-[#FF7A1A] transition-all duration-1000" style="width: {{ $progresso }}%"></div>
                    
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-xl text-[#0A1128] group-hover:text-[#FF7A1A] transition-colors line-clamp-1" title="{{ $projeto->nome }}">{{ $projeto->nome }}</h3>
                        
                        @if($projeto->status === 'concluido')
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold uppercase tracking-wider rounded-md">Concluído</span>
                        @elseif($projeto->status === 'em_andamento')
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-wider rounded-md">Em Andamento</span>
                        @elseif($projeto->status === 'aguardando_cliente')
                            <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold uppercase tracking-wider rounded-md">Aguardando Você</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider rounded-md">Pendente</span>
                        @endif
                    </div>

                    <p class="text-sm text-slate-500 mb-6 line-clamp-2 min-h-[40px]">{{ $projeto->descricao ?? 'Sem descrição fornecida.' }}</p>

                    <div class="flex items-center justify-between border-t border-slate-100 pt-4 mt-auto">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center border border-slate-200">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#0A1128]">{{ $progresso }}% Concluído</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $concluidas }} de {{ $total }} tarefas</p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-0.5">Previsão</p>
                            <p class="text-xs font-bold text-[#0A1128]">{{ $projeto->data_previsao ? $projeto->data_previsao->format('d/m/Y') : 'A definir' }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                    <div class="w-16 h-16 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#0A1128] mb-1">Nenhum projeto ativo</h3>
                    <p class="text-slate-500 text-sm">Você ainda não possui projetos ou ordens de serviço em andamento.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
