<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Integração Google Meu Negócio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alertas de Sucesso e Erro --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if(!empty($apiError))
                <div class="mb-6 p-4 rounded-xl bg-orange-50 border border-orange-200 text-orange-800 font-medium shadow-sm">
                    <p class="font-bold mb-1">Aviso do Google API:</p>
                    <p class="text-sm">{{ $apiError }}</p>
                    <p class="text-xs mt-2 opacity-80">Verifique se as APIs do Google Meu Negócio estão ativadas no Google Cloud Console.</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if(!$isConnected)
                        <!-- ESTADO: NÃO CONECTADO -->
                        <div class="text-center py-10">
                            <div class="flex justify-center mb-4">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">Conecte sua conta do Google</h3>
                            <p class="text-gray-600 mb-6">Integre seu perfil do Google Meu Negócio para gerenciar postagens e visualizar métricas de desempenho diretamente daqui.</p>
                            
                            <a href="{{ route('customer.google-business.connect') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/>
                                </svg>
                                Conectar ao Google Meu Negócio
                            </a>
                        </div>
                    @else
                        <!-- ESTADO: CONECTADO -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Seu Dashboard no Google</h3>
                            <form action="{{ route('customer.google-business.disconnect') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">Desconectar Conta</button>
                            </form>
                        </div>

                        <!-- Seleção do Local -->
                        <div class="mb-8 p-4 bg-gray-50 rounded-lg border">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Selecione o Local (Location)</label>
                            <select id="location" name="location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Selecione uma ficha para gerenciar...</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc['name'] }}">{{ $loc['title'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cards de Métricas Vazios -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Card Impressões -->
                            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex flex-col justify-center items-center h-32">
                                <span class="text-gray-500 text-sm font-medium uppercase tracking-wide">Impressões (30 dias)</span>
                                <span class="text-3xl font-bold text-gray-800 mt-2">--</span>
                            </div>

                            <!-- Card Interações -->
                            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex flex-col justify-center items-center h-32">
                                <span class="text-gray-500 text-sm font-medium uppercase tracking-wide">Cliques p/ Site</span>
                                <span class="text-3xl font-bold text-gray-800 mt-2">--</span>
                            </div>

                            <!-- Card Ligações -->
                            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex flex-col justify-center items-center h-32">
                                <span class="text-gray-500 text-sm font-medium uppercase tracking-wide">Ligações Feitas</span>
                                <span class="text-3xl font-bold text-gray-800 mt-2">--</span>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
