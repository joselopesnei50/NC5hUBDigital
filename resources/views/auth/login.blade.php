<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8 text-center lg:text-left">
        <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Portal Executivo</p>
        <h2 class="font-display font-extrabold text-3xl text-[#0A1128] mb-2 tracking-tight">Acesse o BruceOS</h2>
        <p class="text-sm text-gray-500">Insira suas credenciais corporativas para acessar sua central de operações.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">E-mail Corporativo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-[#0A1128] focus:bg-white focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] outline-none transition-all" placeholder="voce@empresa.com.br">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Senha</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-[#FF7A1A] hover:underline transition-colors" href="{{ route('password.request') }}">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-[#0A1128] focus:bg-white focus:ring-1 focus:ring-[#FF7A1A] focus:border-[#FF7A1A] outline-none transition-all" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center pt-1">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#FF7A1A] focus:ring-[#FF7A1A] w-4 h-4" name="remember">
            <label for="remember_me" class="ml-2 text-sm text-gray-600 font-medium">Manter conectado neste dispositivo</label>
        </div>

        <button type="submit" class="w-full bg-[#0A1128] hover:bg-[#FF7A1A] text-white py-3.5 px-4 rounded-xl text-sm font-bold tracking-wide transition-all shadow-lg shadow-[#0A1128]/15 cursor-pointer">
            Entrar no BruceOS
        </button>
    </form>
</x-guest-layout>
