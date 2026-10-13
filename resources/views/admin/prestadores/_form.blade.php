@csrf

@if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
        <p class="font-bold text-sm mb-1">Corrija os campos abaixo:</p>
        <ul class="list-disc pl-5 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php $p = $prestador ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-6 gap-6">
    <div class="md:col-span-4">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome / Razão social *</label>
        <input type="text" name="nome" value="{{ old('nome', $p->nome ?? '') }}" required
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Tipo *</label>
        <select name="tipo_pessoa" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            @foreach(['PF' => 'Pessoa Física', 'PJ' => 'Pessoa Jurídica'] as $v => $l)
                <option value="{{ $v }}" @selected(old('tipo_pessoa', $p->tipo_pessoa ?? 'PF') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-3">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">CPF / CNPJ</label>
        <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj', $p->cpf_cnpj ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-3">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">RG / Inscrição Estadual</label>
        <input type="text" name="rg_ie" value="{{ old('rg_ie', $p->rg_ie ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-3">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">E-mail</label>
        <input type="email" name="email" value="{{ old('email', $p->email ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-3">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Telefone</label>
        <input type="text" name="telefone" value="{{ old('telefone', $p->telefone ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-3">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Categoria / Área</label>
        <input type="text" name="categoria" value="{{ old('categoria', $p->categoria ?? '') }}" placeholder="Ex: Designer, Dev, Social Media..."
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-3 flex items-end">
        <label class="inline-flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="ativo" value="0">
            <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $p->ativo ?? true))
                   class="w-5 h-5 rounded border-gray-300 text-[#FF7A1A] focus:ring-[#FF7A1A]">
            <span class="text-sm font-bold text-[#0A1128]">Prestador ativo</span>
        </label>
    </div>

    <div class="md:col-span-6">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Endereço</label>
        <input type="text" name="endereco" value="{{ old('endereco', $p->endereco ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-3">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Cidade</label>
        <input type="text" name="cidade" value="{{ old('cidade', $p->cidade ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-1">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">UF</label>
        <input type="text" maxlength="2" name="uf" value="{{ old('uf', $p->uf ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] uppercase">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">CEP</label>
        <input type="text" name="cep" value="{{ old('cep', $p->cep ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>
</div>

<div class="mt-8 pt-6 border-t border-gray-200">
    <h3 class="text-sm font-bold text-[#0A1128] uppercase tracking-wide mb-4">Dados bancários</h3>
    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Banco</label>
            <input type="text" name="banco" value="{{ old('banco', $p->banco ?? '') }}"
                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        </div>
        <div class="md:col-span-1">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Agência</label>
            <input type="text" name="agencia" value="{{ old('agencia', $p->agencia ?? '') }}"
                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        </div>
        <div class="md:col-span-1">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Conta</label>
            <input type="text" name="conta" value="{{ old('conta', $p->conta ?? '') }}"
                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Chave PIX</label>
            <input type="text" name="chave_pix" value="{{ old('chave_pix', $p->chave_pix ?? '') }}"
                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        </div>
    </div>
</div>

<div class="mt-6">
    <label class="block text-sm font-bold text-[#0A1128] mb-2">Observações internas</label>
    <textarea name="notas" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">{{ old('notas', $p->notas ?? '') }}</textarea>
</div>
