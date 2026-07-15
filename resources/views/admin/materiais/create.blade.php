<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Enviar Material para Aprovação
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-3xl">
        <form action="{{ route('admin.materiais.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Selecione o Cliente</label>
                    <select name="cliente_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="">Cliente...</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->razao_social }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Título do Material</label>
                    <input type="text" name="titulo" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Ex: Campanha de Dia dos Pais - Imagens">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Link do Arquivo (Drive, Figma, Canva)</label>
                    <input type="url" name="arquivo_path" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="https://...">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Tipo de Material</label>
                    <select name="tipo" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                        <option value="imagem">Imagem / Banner</option>
                        <option value="video">Vídeo</option>
                        <option value="texto">Texto / Copy</option>
                        <option value="layout">Layout Completo</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.materiais.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Enviar para o Cliente
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
