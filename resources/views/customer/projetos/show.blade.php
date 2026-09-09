<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('customer.projetos.index') }}" class="p-2 bg-white rounded-full hover:bg-slate-50 transition shadow-sm border border-slate-100">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <p class="text-xs font-bold text-[#FF7A1A] uppercase tracking-widest mb-1">Acompanhamento do Projeto</p>
                <h2 class="font-display font-bold text-3xl text-[#0A1128] leading-tight">{{ $projeto->nome }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detalhes do Projeto -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50">
                <h3 class="font-bold text-lg text-[#0A1128] mb-4">Informações</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Status Atual</p>
                        @if($projeto->status === 'concluido')
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-md inline-block">Concluído</span>
                        @elseif($projeto->status === 'em_andamento')
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider rounded-md inline-block">Em Andamento</span>
                        @elseif($projeto->status === 'aguardando_cliente')
                            <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold uppercase tracking-wider rounded-md inline-block">Aguardando Você</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider rounded-md inline-block">Pendente</span>
                        @endif
                    </div>

                    <div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Previsão de Entrega</p>
                        <p class="font-bold text-[#0A1128]">{{ $projeto->data_previsao ? $projeto->data_previsao->format('d/m/Y') : 'A definir' }}</p>
                    </div>

                    @if($projeto->descricao)
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Descrição</p>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $projeto->descricao }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100">
                    <a href="{{ route('customer.projetos.edit', $projeto) }}" class="text-sm font-bold text-slate-400 hover:text-[#0A1128] transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Editar Informações
                    </a>
                </div>
            </div>

            <!-- Progresso Geral -->
            @php
                $total = $projeto->tarefas->count();
                $concluidas = $projeto->tarefas->where('concluida', true)->count();
                $progresso = $total > 0 ? round(($concluidas / $total) * 100) : 0;
            @endphp
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50">
                <h3 class="font-bold text-lg text-[#0A1128] mb-4">Progresso</h3>
                <div class="flex items-end justify-between mb-2">
                    <p class="text-3xl font-display font-bold text-[#0A1128]">{{ $progresso }}%</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider pb-1">{{ $concluidas }} / {{ $total }}</p>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-[#FF7A1A] h-3 rounded-full transition-all duration-1000" style="width: {{ $progresso }}%"></div>
                </div>
            </div>
        </div>

        <!-- Checklist de Tarefas -->
        <div class="lg:col-span-2 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-lg text-[#0A1128]">Linha do Tempo / Etapas</h3>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3 mb-8">
                        @forelse($projeto->tarefas as $tarefa)
                            <div class="flex items-center justify-between p-3 rounded-xl border {{ $tarefa->concluida ? 'bg-green-50 border-green-100' : 'bg-white border-slate-100 hover:border-slate-300' }} transition-colors">
                                <div class="flex items-center gap-3">
                                    <form action="{{ route('customer.projetos.tarefas.toggle', $tarefa) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-6 h-6 rounded border flex items-center justify-center transition-colors {{ $tarefa->concluida ? 'bg-green-500 border-green-500 text-white' : 'border-slate-300 text-transparent hover:border-[#FF7A1A]' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <span class="{{ $tarefa->concluida ? 'line-through text-slate-400' : 'font-medium text-[#0A1128]' }}">
                                        {{ $tarefa->titulo }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-400 py-4 text-sm">Nenhuma etapa definida para este projeto ainda.</p>
                        @endforelse
                    </div>

                    <!-- Add new task -->
                    <form action="{{ route('customer.projetos.tarefas.store', $projeto) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="titulo" placeholder="Adicionar nova etapa..." required class="flex-1 rounded-xl border-slate-200 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] text-sm">
                        <button type="submit" class="px-4 py-2 bg-[#0A1128] text-white text-sm font-bold rounded-xl hover:bg-[#FF7A1A] transition">
                            Adicionar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
