<x-admin-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Agenda</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">
                {{ $appointment->exists ? 'Editar agendamento' : 'Novo agendamento' }}
            </h2>
        </div>
    </x-slot>

    <form method="POST" action="{{ $appointment->exists ? route('admin.hub.agenda.update', $appointment) : route('admin.hub.agenda.store') }}" class="space-y-6">
        @csrf
        @if($appointment->exists) @method('PUT') @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3 rounded-xl text-sm">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm space-y-5">
            <div>
                <label class="block text-sm font-bold text-ink mb-1">Título *</label>
                <input type="text" name="titulo" value="{{ old('titulo', $appointment->titulo) }}" maxlength="160" required
                       class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none"
                       placeholder="Ex: Consulta de rotina — Maria">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-ink mb-1">Data *</label>
                    <input type="date" name="scheduled_date"
                           value="{{ old('scheduled_date', $appointment->scheduled_at?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink mb-1">Hora *</label>
                    <input type="time" name="scheduled_time"
                           value="{{ old('scheduled_time', $appointment->scheduled_at?->format('H:i')) }}" required
                           class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink mb-1">Duração (min) *</label>
                    <input type="number" name="duration_minutes"
                           value="{{ old('duration_minutes', $appointment->duration_minutes ?: 60) }}" min="5" max="1440" required
                           class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-ink mb-1">Cliente (opcional)</label>
                <select name="cliente_final_id" class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                    <option value="">— sem cliente cadastrado —</option>
                    @foreach($clientesFinais as $cf)
                        <option value="{{ $cf->id }}" {{ (int) old('cliente_final_id', $appointment->cliente_final_id) === $cf->id ? 'selected' : '' }}>
                            {{ $cf->nome_responsavel ?: $cf->nome_empresa }} @if($cf->telefone) ({{ $cf->telefone }})@endif
                        </option>
                    @endforeach
                </select>
            </div>

            @if($resources->isNotEmpty())
                <div>
                    <label class="block text-sm font-bold text-ink mb-1">{{ $resourceLabel }} {{ $resources->count() > 1 ? '*' : '' }}</label>
                    <select name="resource_id" class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none" @if($resources->count() > 1) required @endif>
                        @if($resources->count() === 1)
                            <option value="{{ $resources->first()->id }}" selected>{{ $resources->first()->nome }}</option>
                        @else
                            <option value="">— selecione —</option>
                            @foreach($resources as $r)
                                <option value="{{ $r->id }}" {{ (int) old('resource_id', $appointment->resource_id) === $r->id ? 'selected' : '' }}>
                                    {{ $r->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @if($resources->count() === 1)
                        <p class="text-slate text-xs mt-1">Único cadastrado — selecionado automaticamente. Pra ter mais opções, <a href="{{ route('admin.hub.agenda.recursos.index') }}" class="text-bruce font-bold hover:underline">adicione em {{ strtolower($resourceLabel) }}s</a>.</p>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-ink mb-1">Nome do contato (se não cadastrado)</label>
                    <input type="text" name="contato_nome" value="{{ old('contato_nome', $appointment->contato_nome) }}" maxlength="160"
                           class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-ink mb-1">Telefone</label>
                    <input type="text" name="contato_telefone" value="{{ old('contato_telefone', $appointment->contato_telefone) }}" maxlength="30"
                           class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-ink mb-1">Descrição</label>
                <textarea name="descricao" rows="3"
                          class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none"
                          placeholder="O que será feito, materiais, referência...">{{ old('descricao', $appointment->descricao) }}</textarea>
            </div>

            @if($appointment->exists)
                <div>
                    <label class="block text-sm font-bold text-ink mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                        @foreach(\App\Models\Appointment::STATUSES as $st)
                            <option value="{{ $st }}" {{ old('status', $appointment->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-sm font-bold text-ink mb-1">Notas internas</label>
                <textarea name="notes" rows="2"
                          class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none"
                          placeholder="Observações que só você vê">{{ old('notes', $appointment->notes) }}</textarea>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.hub.agenda.index') }}" class="text-slate hover:text-ink text-sm">← Voltar</a>
            <button type="submit" class="px-6 py-3 bg-bruce hover:bg-bruce/90 text-white rounded-xl font-bold text-sm transition-colors">
                {{ $appointment->exists ? 'Salvar alterações' : 'Criar agendamento' }}
            </button>
        </div>
    </form>
</x-admin-layout>
