<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Editar Prestador — {{ $prestador->nome }}</h2>
            <form action="{{ route('admin.prestadores.destroy', $prestador) }}" method="POST" onsubmit="return confirm('Remover este prestador? Contratos vinculados também serão apagados.')">
                @csrf @method('DELETE')
                <button class="text-rose-600 hover:text-rose-800 font-bold text-sm">Excluir prestador</button>
            </form>
        </div>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-5xl">
        <form action="{{ route('admin.prestadores.update', $prestador) }}" method="POST">
            @method('PUT')
            @include('admin.prestadores._form')

            <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
                <a href="{{ route('admin.prestadores.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm transition-colors">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
