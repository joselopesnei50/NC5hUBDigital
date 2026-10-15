<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Agenda</p>
                <h2 class="font-display font-bold text-3xl text-ink leading-tight">{{ $label }}s</h2>
                <p class="text-slate text-sm mt-1">Cadastre quem/o quê recebe agendamentos. Com dois ou mais ativos, o bot pergunta ao cliente final qual escolher.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('customer.agenda.index') }}" class="text-slate hover:text-ink text-sm">← Agenda</a>
                <a href="{{ route('customer.agenda.recursos.create') }}"
                   class="px-5 py-2.5 bg-bruce hover:bg-bruce/90 text-white rounded-xl font-bold text-sm transition-colors">
                    + Novo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl text-sm">{{ session('success') }}</div>
        @endif

        @if($resources->isEmpty())
            <div class="bg-mist/60 border border-black/5 rounded-2xl p-8 text-center text-slate text-sm">
                Nenhum {{ strtolower($label) }} cadastrado. <a href="{{ route('customer.agenda.recursos.create') }}" class="text-bruce font-bold hover:underline">Cadastrar o primeiro</a>.
                <p class="text-xs mt-2">Enquanto não houver nenhum, a agenda funciona em modo único (sem divisão).</p>
            </div>
        @else
            <div class="bg-white border border-black/5 rounded-3xl shadow-sm divide-y divide-black/5">
                @foreach($resources as $r)
                    <div class="p-5 flex items-center gap-4 flex-wrap">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold {{ $r->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                            {{ strtoupper(substr($r->nome, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-ink">{{ $r->nome }}</p>
                                @if(!$r->is_active)
                                    <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">inativo</span>
                                @endif
                            </div>
                            @if($r->descricao)<p class="text-sm text-slate mt-1">{{ $r->descricao }}</p>@endif
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('customer.agenda.recursos.edit', $r) }}" class="text-slate hover:text-ink text-sm">Editar</a>
                            <form method="POST" action="{{ route('customer.agenda.recursos.destroy', $r) }}" onsubmit="return confirm('Excluir?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-700 hover:text-rose-900 text-sm">Excluir</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
