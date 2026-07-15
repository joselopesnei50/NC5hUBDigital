<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-10 text-center lg:text-left">
        <h2 class="text-3xl font-light text-[#0a1128] mb-2 tracking-tight">Bem-vindo de volta</h2>
        <p class="text-gray-500">Insira suas credenciais para acessar sua central.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail Corporativo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm bg-gray-50/50" placeholder="voce@empresa.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-blue-600 hover:text-blue-500 transition-colors" href="{{ route('password.request') }}">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>
            
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm bg-gray-50/50" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mt-4">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4" name="remember">
            <label for="remember_me" class="ml-2 text-sm text-gray-600">Manter conectado</label>
        </div>

        <button type="submit" class="w-full bg-[#0a1128] text-white py-3.5 px-4 rounded-lg font-medium tracking-wide hover:bg-blue-900 transition-colors shadow-lg shadow-blue-900/20">
            Acessar Plataforma
        </button>
        
    </form>
</x-guest-layout>
