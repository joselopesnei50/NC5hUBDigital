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

@php
    $t = $termo ?? null;
    $initialMenor = (bool) old('cedente_menor', $t->cedente_menor ?? false);
    $initialConteudo = old('conteudo', $t->conteudo ?? ($initialMenor ? $templateMenor : $templateAdulto));
@endphp

<div x-data="{
    menor: {{ $initialMenor ? 'true' : 'false' }},
    templateAdulto: @js($templateAdulto),
    templateMenor: @js($templateMenor),
    useDefaultTemplate() {
        const area = document.getElementById('termo-conteudo');
        if (!area) return;
        area.value = this.menor ? this.templateMenor : this.templateAdulto;
    },
    onMenorChange() {
        const area = document.getElementById('termo-conteudo');
        if (!area) return;
        const current = area.value.trim();
        if (current === '' || current === this.templateAdulto.trim() || current === this.templateMenor.trim()) {
            area.value = this.menor ? this.templateMenor : this.templateAdulto;
        }
    }
}">

    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
        <div class="md:col-span-3">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Cliente vinculado (opcional)</label>
            <select name="cliente_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                <option value="">NC5 Hub Digital (Uso interno / institucional)</option>
                @foreach($clientes as $cli)
                    <option value="{{ $cli->id }}" @selected((int) old('cliente_id', $t->cliente_id ?? ($clienteSelecionado ?? 0)) === $cli->id)>
                        {{ $cli->razao_social }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-[#8A8F9C] mt-1">Deixe em branco quando a produção for para a própria NC5.</p>
        </div>

        <div class="md:col-span-3">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Título do termo *</label>
            <input type="text" name="titulo" value="{{ old('titulo', $t->titulo ?? '') }}" required
                   placeholder="Ex: Autorização de Imagem — Campanha Institucional 2026"
                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Projeto / Gravação de referência</label>
            <input type="text" name="projeto_referencia" value="{{ old('projeto_referencia', $t->projeto_referencia ?? '') }}"
                   placeholder="Ex: Gravação Reels Maio / Depoimento"
                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Data da captação</label>
            <input type="date" name="data_captacao" value="{{ old('data_captacao', optional($t?->data_captacao)->format('Y-m-d') ?? '') }}"
                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Local da captação</label>
            <input type="text" name="local_captacao" value="{{ old('local_captacao', $t->local_captacao ?? '') }}"
                   placeholder="Ex: Estúdio NC5 — São Paulo/SP"
                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        </div>
    </div>

    <div class="mt-8 pt-6 border-t border-gray-200">
        <h3 class="text-sm font-bold text-[#0A1128] uppercase tracking-wide mb-4">Dados do Cedente (Pessoa filmada / fotografada)</h3>

        <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
            <div class="md:col-span-3">
                <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome completo do cedente *</label>
                <input type="text" name="cedente_nome" value="{{ old('cedente_nome', $t->cedente_nome ?? '') }}" required
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-bold text-[#0A1128] mb-2">CPF do cedente *</label>
                <input type="text" name="cedente_cpf" value="{{ old('cedente_cpf', $t->cedente_cpf ?? '') }}" required
                       placeholder="000.000.000-00"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-[#0A1128] mb-2">RG (opcional)</label>
                <input type="text" name="cedente_rg" value="{{ old('cedente_rg', $t->cedente_rg ?? '') }}"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-[#0A1128] mb-2">Telefone / WhatsApp</label>
                <input type="text" name="cedente_telefone" value="{{ old('cedente_telefone', $t->cedente_telefone ?? '') }}"
                       placeholder="(11) 99999-9999"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-[#0A1128] mb-2">E-mail</label>
                <input type="email" name="cedente_email" value="{{ old('cedente_email', $t->cedente_email ?? '') }}"
                       placeholder="email@exemplo.com"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-[#0A1128] mb-2">Endereço completo</label>
                <input type="text" name="cedente_endereco" value="{{ old('cedente_endereco', $t->cedente_endereco ?? '') }}"
                       placeholder="Rua, número, bairro, cidade/UF, CEP"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t border-gray-200">
        <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 hover:border-[#FF7A1A] cursor-pointer transition-colors">
            <input type="hidden" name="cedente_menor" value="0">
            <input type="checkbox" name="cedente_menor" value="1"
                   x-model="menor" @change="onMenorChange()"
                   @checked($initialMenor)
                   class="mt-1 w-5 h-5 rounded border-gray-300 text-[#FF7A1A] focus:ring-[#FF7A1A]">
            <div>
                <div class="text-sm font-bold text-[#0A1128]">O cedente é menor de 18 anos</div>
                <div class="text-xs text-[#8A8F9C] mt-1">Quando marcado, os dados do responsável legal são obrigatórios e a assinatura será feita pelo responsável (com base no ECA).</div>
            </div>
        </label>

        <div x-show="menor" x-transition class="mt-4 p-6 rounded-2xl bg-indigo-50/50 border border-indigo-200 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-bold text-[#0A1128] mb-2">Nome do responsável legal *</label>
                <input type="text" name="responsavel_nome" value="{{ old('responsavel_nome', $t->responsavel_nome ?? '') }}"
                       :required="menor"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>

            <div>
                <label class="block text-sm font-bold text-[#0A1128] mb-2">CPF do responsável legal *</label>
                <input type="text" name="responsavel_cpf" value="{{ old('responsavel_cpf', $t->responsavel_cpf ?? '') }}"
                       :required="menor" placeholder="000.000.000-00"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>

            <div>
                <label class="block text-sm font-bold text-[#0A1128] mb-2">Parentesco / Vínculo *</label>
                <input type="text" name="responsavel_parentesco" value="{{ old('responsavel_parentesco', $t->responsavel_parentesco ?? '') }}"
                       :required="menor" placeholder="Ex: Mãe, Pai, Tutor(a) legal"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            </div>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
            <div>
                <label class="block text-sm font-bold text-[#0A1128]">Texto do Termo de Autorização *</label>
                <p class="text-xs text-[#8A8F9C]">
                    Placeholders como <code>@{{cedente_nome}}</code>, <code>@{{cedente_cpf}}</code>, <code>@{{projeto}}</code>, <code>@{{data_captacao}}</code>, <code>@{{local}}</code> e <code>@{{cliente}}</code> são substituídos automaticamente ao salvar. Você também pode editar o texto livremente.
                </p>
            </div>
            <button type="button" @click="useDefaultTemplate()" class="text-xs font-bold text-[#FF7A1A] hover:underline whitespace-nowrap">
                Restaurar modelo padrão
            </button>
        </div>

        <textarea id="termo-conteudo" name="conteudo" rows="16"
                  class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] font-mono text-xs leading-relaxed">{{ $initialConteudo }}</textarea>
    </div>

    @isset($showStatus)
        <div class="mt-6">
            <label class="block text-sm font-bold text-[#0A1128] mb-2">Status do termo</label>
            <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
                @foreach(['rascunho' => 'Rascunho', 'enviado' => 'Enviado', 'cancelado' => 'Cancelado'] as $s => $label)
                    <option value="{{ $s }}" @selected(old('status', $t->status ?? 'rascunho') === $s)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    @endisset

</div>
