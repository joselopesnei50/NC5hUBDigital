<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Novo Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('customer.clientes-finais.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nome Empresa --}}
                            <div>
                                <label for="nome_empresa" class="block font-medium text-sm text-gray-700">Nome da Empresa *</label>
                                <input type="text" name="nome_empresa" id="nome_empresa" value="{{ old('nome_empresa') }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('nome_empresa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nome Responsável --}}
                            <div>
                                <label for="nome_responsavel" class="block font-medium text-sm text-gray-700">Nome do Responsável</label>
                                <input type="text" name="nome_responsavel" id="nome_responsavel" value="{{ old('nome_responsavel') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('nome_responsavel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Telefone --}}
                            <div>
                                <label for="telefone" class="block font-medium text-sm text-gray-700">Telefone / WhatsApp</label>
                                <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('telefone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- E-mail --}}
                            <div>
                                <label for="email" class="block font-medium text-sm text-gray-700">E-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Cidade --}}
                            <div>
                                <label for="cidade" class="block font-medium text-sm text-gray-700">Cidade</label>
                                <input type="text" name="cidade" id="cidade" value="{{ old('cidade') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('cidade') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Datas de Aniversário --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="data_aniversario_empresa" class="block font-medium text-sm text-gray-700">Fundação da Empresa</label>
                                    <input type="date" name="data_aniversario_empresa" id="data_aniversario_empresa" value="{{ old('data_aniversario_empresa') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @error('data_aniversario_empresa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="data_aniversario_responsavel" class="block font-medium text-sm text-gray-700">Aniversário Responsável</label>
                                    <input type="date" name="data_aniversario_responsavel" id="data_aniversario_responsavel" value="{{ old('data_aniversario_responsavel') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @error('data_aniversario_responsavel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            {{-- Últimos Pedidos --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="ultimos_pedidos" class="block font-medium text-sm text-gray-700">Últimos Pedidos / Histórico</label>
                                <textarea name="ultimos_pedidos" id="ultimos_pedidos" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('ultimos_pedidos') }}</textarea>
                                @error('ultimos_pedidos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('customer.clientes-finais.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Salvar Cliente
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
