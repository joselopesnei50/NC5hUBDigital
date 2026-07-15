<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Escrever Novo Artigo
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-4xl">
        <form action="{{ route('admin.posts.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Título do Artigo</label>
                    <input type="text" name="titulo" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Como o Marketing de Performance muda o jogo...">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Conteúdo do Post</label>
                    <textarea name="conteudo" rows="15" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="publicado">Publicar Agora</option>
                        <option value="rascunho">Salvar como Rascunho</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.posts.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Publicar Artigo
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
