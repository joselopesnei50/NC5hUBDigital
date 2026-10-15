<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Agenda</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">
                {{ $resource->exists ? 'Editar' : 'Novo' }} {{ strtolower($label) }}
            </h2>
        </div>
    </x-slot>

    <form method="POST" action="{{ $resource->exists ? route('customer.agenda.recursos.update', $resource) : route('customer.agenda.recursos.store') }}" class="space-y-6">
        @csrf
        @if($resource->exists) @method('PUT') @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3 rounded-xl text-sm">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm space-y-5">
            <div>
                <label class="block text-sm font-bold text-ink mb-1">Nome *</label>
                <input type="text" name="nome" value="{{ old('nome', $resource->nome) }}" maxlength="120" required
                       class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none"
                       placeholder="Ex: Dr. Silva, Cadeira 1, Mecânico João, Box 2">
            </div>
            <div>
                <label class="block text-sm font-bold text-ink mb-1">Descrição (opcional)</label>
                <input type="text" name="descricao" value="{{ old('descricao', $resource->descricao) }}" maxlength="255"
                       class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none"
                       placeholder="Ex: Especialidade, horário diferenciado, observações internas">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-ink mb-1">Ordem de exibição</label>
                    <input type="number" name="ordem" value="{{ old('ordem', $resource->ordem) }}" min="0" max="999"
                           class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                    <p class="text-slate text-xs mt-1">Menor número aparece primeiro.</p>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $resource->is_active ?: !$resource->exists) ? 'checked' : '' }} class="w-5 h-5 rounded">
                        <span class="text-sm font-bold text-ink">Ativo (aceita agendamentos)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('customer.agenda.recursos.index') }}" class="text-slate hover:text-ink text-sm">← Voltar</a>
            <button type="submit" class="px-6 py-3 bg-bruce hover:bg-bruce/90 text-white rounded-xl font-bold text-sm transition-colors">
                {{ $resource->exists ? 'Salvar alterações' : 'Criar' }}
            </button>
        </div>
    </form>
</x-app-layout>
