<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Compras</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Editar fornecedor</h2>
            <p class="text-slate text-sm mt-1">Ajuste os dados de <span class="font-bold text-ink">{{ $fornecedor->nome }}</span>.</p>
        </div>
    </x-slot>

    <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm max-w-3xl">
        <form action="{{ route('customer.fornecedores.update', $fornecedor) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('customer.fornecedores._form', ['submitLabel' => 'Salvar alterações'])
        </form>
    </div>
</x-app-layout>
