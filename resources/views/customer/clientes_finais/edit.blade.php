<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cliente: ') }} {{ $clienteFinal->nome_empresa }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('customer.clientes-finais.update', $clienteFinal->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nome Empresa --}}
                            <div>
                                <label for="nome_empresa" class="block font-medium text-sm text-gray-700">Nome da Empresa *</label>
                                <input type="text" name="nome_empresa" id="nome_empresa" value="{{ old('nome_empresa', $clienteFinal->nome_empresa) }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('nome_empresa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nome Responsável --}}
                            <div>
                                <label for="nome_responsavel" class="block font-medium text-sm text-gray-700">Nome do Responsável</label>
                                <input type="text" name="nome_responsavel" id="nome_responsavel" value="{{ old('nome_responsavel', $clienteFinal->nome_responsavel) }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('nome_responsavel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Telefone --}}
                            <div>
                                <label for="telefone" class="block font-medium text-sm text-gray-700">Telefone / WhatsApp</label>
                                <input type="text" name="telefone" id="telefone" value="{{ old('telefone', $clienteFinal->telefone) }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('telefone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- E-mail --}}
                            <div>
                                <label for="email" class="block font-medium text-sm text-gray-700">E-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $clienteFinal->email) }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Cidade --}}
                            <div>
                                <label for="cidade" class="block font-medium text-sm text-gray-700">Cidade</label>
                                <input type="text" name="cidade" id="cidade" value="{{ old('cidade', $clienteFinal->cidade) }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('cidade') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Datas de Aniversário --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="data_aniversario_empresa" class="block font-medium text-sm text-gray-700">Fundação da Empresa</label>
                                    <input type="date" name="data_aniversario_empresa" id="data_aniversario_empresa" value="{{ old('data_aniversario_empresa', $clienteFinal->data_aniversario_empresa ? $clienteFinal->data_aniversario_empresa->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @error('data_aniversario_empresa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="data_aniversario_responsavel" class="block font-medium text-sm text-gray-700">Aniversário Responsável</label>
                                    <input type="date" name="data_aniversario_responsavel" id="data_aniversario_responsavel" value="{{ old('data_aniversario_responsavel', $clienteFinal->data_aniversario_responsavel ? $clienteFinal->data_aniversario_responsavel->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @error('data_aniversario_responsavel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            {{-- Últimos Pedidos --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="ultimos_pedidos" class="block font-medium text-sm text-gray-700">Últimos Pedidos / Histórico</label>
                                <textarea name="ultimos_pedidos" id="ultimos_pedidos" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('ultimos_pedidos', $clienteFinal->ultimos_pedidos) }}</textarea>
                                @error('ultimos_pedidos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('customer.clientes-finais.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>

                    @if(isset($icsUrl))
                        <div class="mt-8 pt-6 border-t border-gray-200" x-data="{ copied: false }">
                            <h4 class="font-bold text-sm text-gray-900 mb-1">Link da agenda deste cliente</h4>
                            <p class="text-xs text-gray-500 mb-3">Compartilhe este link com o cliente pra ele sincronizar os compromissos no Google Calendar / Outlook / Apple.</p>
                            <div class="flex items-center gap-2 flex-wrap">
                                <input type="text" readonly value="{{ $icsUrl }}" x-ref="ics"
                                       class="flex-1 min-w-[280px] px-3 py-2 bg-gray-50 border border-gray-200 rounded text-xs font-mono text-gray-600">
                                <button type="button"
                                        @click="navigator.clipboard.writeText($refs.ics.value); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold">
                                    <span x-show="!copied">Copiar</span>
                                    <span x-show="copied" x-cloak>Copiado!</span>
                                </button>
                                <form method="POST" action="{{ route('customer.calendar.cliente-final.regenerate', $clienteFinal) }}" onsubmit="return confirm('O link antigo deixará de funcionar. Confirma?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-white border border-gray-200 hover:border-red-400 text-red-700 rounded text-xs font-bold">Regenerar</button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
