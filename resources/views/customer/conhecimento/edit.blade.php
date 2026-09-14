<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Base de conhecimento</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Editar documento</h2>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form action="{{ route('customer.conhecimento.update', $item) }}" method="POST" class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="titulo" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Título</label>
                <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $item->titulo) }}" required maxlength="200"
                       class="w-full rounded-2xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm">
                @error('titulo')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="conteudo" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Conteúdo</label>
                <textarea name="conteudo" id="conteudo" rows="12" required maxlength="20000"
                          class="w-full rounded-2xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm font-mono">{{ old('conteudo', $item->conteudo) }}</textarea>
                @error('conteudo')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="ativo" id="ativo" value="1" {{ $item->ativo ? 'checked' : '' }}
                       class="rounded border-gray-300 text-bruce focus:ring-bruce">
                <label for="ativo" class="text-sm text-ink">Ativo (o Bruce considera este documento nas buscas)</label>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-black/5">
                <div class="flex items-center gap-4">
                    <a href="{{ route('customer.conhecimento.index') }}" class="text-slate hover:text-ink font-bold text-sm">← Cancelar</a>
                    <form action="{{ route('customer.conhecimento.destroy', $item) }}" method="POST"
                          onsubmit="return confirm('Excluir este documento da base de conhecimento?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-sm">
                            Excluir documento
                        </button>
                    </form>
                </div>
                <button type="submit" class="px-6 py-3 bg-ink hover:bg-bruce text-white rounded-full font-bold text-sm transition-colors">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
