<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-extrabold text-[#FF7A1A] uppercase tracking-widest mb-1">Base de Clientes · BruceOS</p>
                <h2 class="font-display font-extrabold text-3xl text-[#0A1128] leading-tight">
                    Novo Cliente
                </h2>
            </div>
            <a href="{{ route('customer.clientes-finais.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#0A1128] border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar à Base de Clientes
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
            <div class="mb-6 pb-5 border-b border-gray-100">
                <h3 class="font-display font-extrabold text-lg text-[#0A1128]">Ficha Cadastral do Cliente</h3>
                <p class="text-sm text-gray-500 mt-0.5">Registre empresas, contatos e datas comemorativas para relacionamento ativo.</p>
            </div>

            <form action="{{ route('customer.clientes-finais.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nome Empresa --}}
                    <div>
                        <label for="nome_empresa" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nome da Empresa / Cliente *</label>
                        <input type="text" name="nome_empresa" id="nome_empresa" value="{{ old('nome_empresa') }}" required placeholder="Ex: Construtora Horizonte Ltda" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('nome_empresa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nome Responsável --}}
                    <div>
                        <label for="nome_responsavel" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nome do Responsável</label>
                        <input type="text" name="nome_responsavel" id="nome_responsavel" value="{{ old('nome_responsavel') }}" placeholder="Ex: Carlos Mendes" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('nome_responsavel') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Telefone --}}
                    <div>
                        <label for="telefone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Telefone / WhatsApp</label>
                        <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}" placeholder="Ex: (11) 99999-9999" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('telefone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- E-mail --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">E-mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="contato@empresa.com.br" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Cidade --}}
                    <div>
                        <label for="cidade" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Cidade / UF</label>
                        <input type="text" name="cidade" id="cidade" value="{{ old('cidade') }}" placeholder="Ex: São Paulo - SP" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                        @error('cidade') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Datas de Aniversário --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="data_aniversario_empresa" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Fundação Empresa</label>
                            <input type="date" name="data_aniversario_empresa" id="data_aniversario_empresa" value="{{ old('data_aniversario_empresa') }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            @error('data_aniversario_empresa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="data_aniversario_responsavel" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Aniv. Responsável</label>
                            <input type="date" name="data_aniversario_responsavel" id="data_aniversario_responsavel" value="{{ old('data_aniversario_responsavel') }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">
                            @error('data_aniversario_responsavel') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Últimos Pedidos --}}
                    <div class="col-span-1 md:col-span-2">
                        <label for="ultimos_pedidos" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Histórico / Observações de Relacionamento</label>
                        <textarea name="ultimos_pedidos" id="ultimos_pedidos" rows="4" placeholder="Anote preferências, histórico comercial ou observações importantes..." class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-[#0A1128] focus:bg-white focus:border-[#FF7A1A] focus:ring-1 focus:ring-[#FF7A1A] outline-none transition-all">{{ old('ultimos_pedidos') }}</textarea>
                        @error('ultimos_pedidos') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('customer.clientes-finais.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm">
                        Salvar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
