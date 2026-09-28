<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Compras</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Novo fornecedor</h2>
            <p class="text-slate text-sm mt-1">Cadastre uma vez, use em todos os lançamentos de contas a pagar.</p>
        </div>
    </x-slot>

    <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm max-w-3xl">
        <form action="{{ route('customer.fornecedores.store') }}" method="POST" class="space-y-6">
            @include('customer.fornecedores._form', ['submitLabel' => 'Cadastrar fornecedor'])
        </form>
    </div>
</x-app-layout>
