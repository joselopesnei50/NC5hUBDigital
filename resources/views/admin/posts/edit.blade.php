<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.posts.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Editar Artigo</h2>
                <p class="text-sm text-[#8A8F9C] mt-1">/blog/{{ $post->slug }}</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-4xl">
        <form action="{{ route('admin.posts.update', $post->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Título</label>
                    <input type="text" name="titulo" value="{{ old('titulo', $post->titulo) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Conteúdo</label>
                    <textarea name="conteudo" rows="18" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">{{ old('conteudo', $post->conteudo) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#E63888] focus:ring-[#E63888]">
                        <option value="publicado" @selected($post->status == 'publicado')>Publicado</option>
                        <option value="rascunho" @selected($post->status == 'rascunho')>Rascunho</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.posts.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">Salvar Artigo</button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100">
            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Remover artigo?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm">Excluir artigo</button>
            </form>
        </div>
    </div>
</x-admin-layout>
