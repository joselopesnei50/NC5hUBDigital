<x-guest-layout>
    <div class="mb-10 text-center lg:text-left">
        <h2 class="text-3xl font-light text-[#0a1128] mb-2 tracking-tight">Crie sua central</h2>
        <p class="text-gray-500">Inicie sua jornada de Controle Absoluto hoje.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm bg-gray-50/50" placeholder="Seu nome">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail Corporativo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm bg-gray-50/50" placeholder="voce@empresa.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha Estratégica</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm bg-gray-50/50" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirme a Senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm bg-gray-50/50" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full bg-[#0a1128] text-white py-3.5 px-4 rounded-lg font-medium tracking-wide hover:bg-blue-900 transition-colors shadow-lg shadow-blue-900/20 mt-4">
            Ativar Conta
        </button>
        
        <div class="mt-6 text-center text-sm text-gray-500">
            Já gerencia seus negócios? 
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-500 transition-colors">Faça login</a>
        </div>
    </form>
</x-guest-layout>
