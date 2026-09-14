<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Base de conhecimento</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">O que o Bruce sabe da sua empresa</h2>
            <p class="text-slate text-sm mt-1">Cadastre políticas, preços, procedimentos e regras. O Bruce consulta isso antes de responder.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white border border-black/5 rounded-3xl p-5 sm:p-6 shadow-sm">
            <form method="GET" action="{{ route('customer.conhecimento.index') }}" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                <div class="flex-1 flex gap-2">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Buscar por título ou conteúdo..."
                           class="flex-1 rounded-full border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                    <button type="submit" class="px-5 py-2 bg-ink hover:bg-bruce text-white rounded-full font-bold text-sm transition-colors">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('customer.conhecimento.index') }}"
                           class="px-4 py-2 bg-mist hover:bg-slate/10 text-slate rounded-full font-bold text-sm transition-colors flex items-center">
                            Limpar
                        </a>
                    @endif
                </div>

                <a href="{{ route('customer.conhecimento.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-bruce hover:bg-ink text-white rounded-full font-bold text-sm transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Novo documento
                </a>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($itens as $item)
                <div class="bg-white border border-black/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h3 class="font-display font-bold text-ink text-base leading-tight flex-1">{{ $item->titulo }}</h3>
                        @if($item->ativo)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider text-slate/70 bg-slate/10 px-2 py-0.5 rounded-full whitespace-nowrap">
                                Pausado
                            </span>
                        @endif
                    </div>
                    <p class="text-slate text-sm leading-relaxed flex-1 line-clamp-4">{{ Str::limit($item->conteudo, 220) }}</p>
                    <div class="mt-4 pt-4 border-t border-black/5 flex items-center justify-between">
                        <span class="text-xs text-slate/60">Atualizado {{ $item->updated_at->diffForHumans() }}</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('customer.conhecimento.edit', $item) }}"
                               class="text-bruce hover:text-ink font-bold text-xs uppercase tracking-wider">Editar</a>
                            <form action="{{ route('customer.conhecimento.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('Excluir este documento da base de conhecimento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs uppercase tracking-wider">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-black/5 rounded-3xl p-10 shadow-sm text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-bruce/10 text-bruce flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-ink text-lg">Sua base de conhecimento está vazia</h3>
                    <p class="text-slate text-sm mt-2 max-w-md mx-auto">
                        Cadastre políticas, preços, procedimentos e informações sobre produtos. Assim o Bruce consegue responder perguntas específicas da sua empresa em vez de dar respostas genéricas.
                    </p>
                    <a href="{{ route('customer.conhecimento.create') }}"
                       class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 bg-ink hover:bg-bruce text-white rounded-full font-bold text-sm transition-colors">
                        Cadastrar primeiro documento
                    </a>
                </div>
            @endforelse
        </div>

        <div>
            {{ $itens->links() }}
        </div>
    </div>
</x-app-layout>
