@csrf

@if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Nome do fornecedor *</label>
        <input type="text" id="nome" name="nome" required value="{{ old('nome', $fornecedor->nome ?? '') }}"
               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
               placeholder="Ex: AWS, Contabilidade XYZ, Papelaria Boa Vista">
    </div>

    <div>
        <label for="cnpj_cpf" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">CNPJ ou CPF</label>
        <input type="text" id="cnpj_cpf" name="cnpj_cpf" value="{{ old('cnpj_cpf', $fornecedor->cnpj_cpf ?? '') }}"
               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm font-mono"
               placeholder="00.000.000/0000-00">
    </div>

    <div>
        <label for="categoria" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Categoria</label>
        <input type="text" id="categoria" name="categoria" value="{{ old('categoria', $fornecedor->categoria ?? '') }}"
               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
               placeholder="Ex: Software, Contabilidade, Material de escritório">
    </div>

    <div>
        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">E-mail</label>
        <input type="email" id="email" name="email" value="{{ old('email', $fornecedor->email ?? '') }}"
               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
               placeholder="contato@fornecedor.com.br">
    </div>

    <div>
        <label for="telefone" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Telefone</label>
        <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $fornecedor->telefone ?? '') }}"
               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
               placeholder="(11) 99999-0000">
    </div>

    <div class="md:col-span-2">
        <label for="endereco" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Endereço</label>
        <input type="text" id="endereco" name="endereco" value="{{ old('endereco', $fornecedor->endereco ?? '') }}"
               class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
               placeholder="Rua, número, cidade — UF">
    </div>

    <div class="md:col-span-2">
        <label for="notas" class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Notas internas</label>
        <textarea id="notas" name="notas" rows="3"
                  class="w-full rounded-xl border-gray-300 focus:border-bruce focus:ring-bruce text-sm"
                  placeholder="Prazo médio de entrega, condição de pagamento, contato-chave...">{{ old('notas', $fornecedor->notas ?? '') }}</textarea>
    </div>
</div>

<div class="flex items-center justify-end gap-3 pt-4 border-t border-black/5">
    <a href="{{ route('customer.fornecedores.index') }}" class="px-5 py-2 text-sm font-bold text-slate hover:text-ink transition-colors">
        Cancelar
    </a>
    <button type="submit" class="px-6 py-2 bg-ink hover:bg-bruce text-white rounded-full text-sm font-bold transition-colors">
        {{ $submitLabel ?? 'Salvar' }}
    </button>
</div>
