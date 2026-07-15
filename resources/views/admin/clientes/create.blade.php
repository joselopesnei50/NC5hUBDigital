<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
            Cadastrar Novo Cliente
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-3xl">
        <form action="{{ route('admin.clientes.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Dados Básicos -->
                <div class="col-span-2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informações de Acesso (Login)</h3>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nome do Responsável</label>
                    <input type="text" name="nome" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ex: João Silva">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">E-mail de Acesso</label>
                    <input type="email" name="email" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="contato@empresa.com">
                </div>

                <!-- Dados da Empresa -->
                <div class="col-span-2 mt-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Dados do Cadastro</h3>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Pessoa</label>
                    <select name="tipo_pessoa" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="PJ">Pessoa Jurídica (CNPJ)</option>
                        <option value="PF">Pessoa Física (CPF)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">CPF / CNPJ</label>
                    <input type="text" name="cpf_cnpj" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="00.000.000/0001-00">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Razão Social ou Nome Fantasia</label>
                    <input type="text" name="razao_social" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Empresa XYZ LTDA">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Telefone (WhatsApp)</label>
                    <input type="text" name="telefone" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="(11) 99999-9999">
                </div>

            </div>

            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.clientes.index') }}" class="text-gray-500 hover:text-gray-900 font-bold text-sm">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg shadow-blue-500/30">
                    Cadastrar Cliente e Gerar Senha
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
