<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Portal do cliente</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Trocar minha senha</h2>
            <p class="text-sm text-slate-500 mt-1">Escolha uma senha nova. Você continua logado após a troca.</p>
        </div>
    </x-slot>

    <div class="max-w-xl">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            @if(session('status') === 'password-updated')
                <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-800">
                    Senha atualizada com sucesso.
                </div>
            @endif

            @if($errors->updatePassword->any())
                <ul class="mb-4 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->updatePassword->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-1">Senha atual</label>
                    <input type="password" name="current_password" required autocomplete="current-password" class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-1">Nova senha</label>
                    <input type="password" name="password" required autocomplete="new-password" class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128]">
                    <p class="text-xs text-slate-500 mt-1">Mínimo 8 caracteres.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-1">Confirmar nova senha</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128]">
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-[#0A1128] text-white rounded-xl text-sm font-bold hover:bg-[#FF7A1A] transition-colors shadow-lg">
                        Salvar nova senha
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
