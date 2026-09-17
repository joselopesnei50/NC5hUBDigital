<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.clientes.show', $cliente->id) }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Editar Cliente</h2>
                <p class="text-sm text-[#8A8F9C] mt-1">{{ $cliente->razao_social }}</p>
            </div>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-3xl">
        <form action="{{ route('admin.clientes.update', $cliente->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <h3 class="text-lg font-bold text-[#0A1128] mb-4 border-b border-gray-100 pb-2">Informações de Acesso</h3>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome do Responsável</label>
                    <input type="text" name="nome" value="{{ old('nome', $cliente->user->name) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#FF7A1A] focus:ring-[#FF7A1A]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">E-mail de Acesso</label>
                    <input type="email" name="email" value="{{ old('email', $cliente->user->email) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#FF7A1A] focus:ring-[#FF7A1A]">
                </div>

                <div class="col-span-2 mt-4">
                    <h3 class="text-lg font-bold text-[#0A1128] mb-4 border-b border-gray-100 pb-2">Dados do Cadastro</h3>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Tipo de Pessoa</label>
                    <select name="tipo_pessoa" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#FF7A1A] focus:ring-[#FF7A1A]">
                        <option value="PJ" @selected(old('tipo_pessoa', $cliente->tipo_pessoa) == 'PJ')>Pessoa Jurídica (CNPJ)</option>
                        <option value="PF" @selected(old('tipo_pessoa', $cliente->tipo_pessoa) == 'PF')>Pessoa Física (CPF)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">CPF / CNPJ</label>
                    <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj', $cliente->cpf_cnpj) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#FF7A1A] focus:ring-[#FF7A1A]">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Razão Social / Nome Fantasia</label>
                    <input type="text" name="razao_social" value="{{ old('razao_social', $cliente->razao_social) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#FF7A1A] focus:ring-[#FF7A1A]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Telefone / WhatsApp</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#FF7A1A] focus:ring-[#FF7A1A]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#FF7A1A] focus:ring-[#FF7A1A]">
                        <option value="ativo" @selected(old('status', $cliente->status) == 'ativo')>Ativo</option>
                        <option value="inativo" @selected(old('status', $cliente->status) == 'inativo')>Inativo</option>
                    </select>
                </div>

                <div class="col-span-2 mt-4">
                    <h3 class="text-lg font-bold text-[#0A1128] mb-4 border-b border-gray-100 pb-2">Vitrine Pública (Home do Site)</h3>
                    <p class="text-sm text-[#8A8F9C] mb-4">Se marcado, a logo aparece na home no bloco "Clientes em Execução". PNG/JPG/SVG/WEBP até 2MB.</p>
                </div>

                <div class="col-span-2">
                    @if($cliente->logo_public_path)
                        <div class="mb-4 flex items-center gap-4 p-3 bg-[#F4F5F7] rounded-xl">
                            <img src="{{ Storage::url($cliente->logo_public_path) }}" alt="Logo atual" class="h-12 w-auto object-contain">
                            <label class="flex items-center gap-2 text-sm text-red-600 font-bold cursor-pointer">
                                <input type="checkbox" name="remover_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Remover logo atual
                            </label>
                        </div>
                    @endif
                    <label class="block text-sm font-bold text-[#0A1128] mb-2">
                        {{ $cliente->logo_public_path ? 'Substituir Logo' : 'Enviar Logo' }}
                    </label>
                    <input type="file" name="logo_public" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="w-full text-sm text-[#8A8F9C] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#F4F5F7] file:text-[#0A1128] hover:file:bg-gray-200">
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-2 text-sm font-bold text-[#0A1128] cursor-pointer">
                        <input type="checkbox" name="exibir_home" value="1" @checked(old('exibir_home', $cliente->exibir_home)) class="rounded border-gray-300 text-[#FF7A1A] focus:ring-[#FF7A1A]">
                        Exibir na home pública (bloco "Clientes em Execução")
                    </label>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.clientes.show', $cliente->id) }}" class="text-[#8A8F9C] hover:text-[#0A1128] font-bold text-sm">Cancelar</a>
                <button type="submit" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                    Salvar Alterações
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100">
            <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST" onsubmit="return confirm('Remover cliente e usuário permanentemente?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm">Excluir cliente permanentemente</button>
            </form>
        </div>
    </div>
</x-admin-layout>
