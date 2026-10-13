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

@php $c = $contrato ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-6 gap-6">
    <div class="md:col-span-6">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Prestador *</label>
        <select name="prestador_id" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            <option value="">Selecione o prestador...</option>
            @foreach($prestadores as $p)
                <option value="{{ $p->id }}" @selected((int) old('prestador_id', $c->prestador_id ?? ($prestadorSelecionado ?? 0)) === $p->id)>
                    {{ $p->nome }}{{ $p->categoria ? ' — ' . $p->categoria : '' }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-[#8A8F9C] mt-1">Não encontrou? <a href="{{ route('admin.prestadores.create') }}" class="text-[#FF7A1A] font-bold hover:underline">Cadastre um novo prestador</a>.</p>
    </div>

    <div class="md:col-span-6">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Título do contrato *</label>
        <input type="text" name="titulo" value="{{ old('titulo', $c->titulo ?? '') }}" required placeholder="Ex: Prestação de serviços de design gráfico — Projeto X"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-6">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Objeto do contrato *</label>
        <textarea name="objeto" rows="4" required placeholder="Descreva detalhadamente os serviços a serem prestados..."
                  class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">{{ old('objeto', $c->objeto ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Valor (R$) *</label>
        <input type="number" step="0.01" min="0" name="valor" value="{{ old('valor', $c->valor ?? '') }}" required
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Forma de pagamento *</label>
        <select name="forma_pagamento" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            @foreach(['mensal' => 'Mensal', 'por_entrega' => 'Por entrega', 'unico' => 'Pagamento único', 'hora' => 'Por hora', 'outro' => 'Outro'] as $v => $l)
                <option value="{{ $v }}" @selected(old('forma_pagamento', $c->forma_pagamento ?? 'mensal') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Periodicidade / dia de pgto</label>
        <input type="text" name="periodicidade" value="{{ old('periodicidade', $c->periodicidade ?? '') }}" placeholder="Ex: todo dia 5"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de início *</label>
        <input type="date" name="data_inicio" value="{{ old('data_inicio', optional($c?->data_inicio)->format('Y-m-d') ?? '') }}" required
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Data de término</label>
        <input type="date" name="data_fim" value="{{ old('data_fim', optional($c?->data_fim)->format('Y-m-d') ?? '') }}"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
        <p class="text-xs text-[#8A8F9C] mt-1">Em branco = contrato por prazo indeterminado.</p>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Prazo de entrega</label>
        <input type="text" name="prazo_entrega" value="{{ old('prazo_entrega', $c->prazo_entrega ?? '') }}" placeholder="Ex: 15 dias úteis"
               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
    </div>
</div>

<div class="mt-8 pt-6 border-t border-gray-200">
    <h3 class="text-sm font-bold text-[#0A1128] uppercase tracking-wide mb-4">Cláusulas padrão</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @php
            $clausulas = [
                'clausula_confidencialidade' => ['Confidencialidade (NDA)', 'O prestador se compromete a manter sigilo sobre informações da NC5 e seus clientes.'],
                'clausula_propriedade_intelectual' => ['Propriedade intelectual', 'Todo material produzido pertence à NC5 após o pagamento.'],
                'clausula_nao_vinculo_empregaticio' => ['Não vínculo empregatício', 'Contrato de natureza civil, sem geração de vínculo CLT.'],
                'clausula_exclusividade' => ['Exclusividade', 'Prestador não atenderá concorrentes diretos durante a vigência.'],
            ];
            $defaults = ['clausula_confidencialidade' => true, 'clausula_propriedade_intelectual' => true, 'clausula_nao_vinculo_empregaticio' => true, 'clausula_exclusividade' => false];
        @endphp
        @foreach($clausulas as $field => [$label, $hint])
            <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 hover:border-[#FF7A1A] cursor-pointer transition-colors">
                <input type="hidden" name="{{ $field }}" value="0">
                <input type="checkbox" name="{{ $field }}" value="1"
                       @checked(old($field, $c->{$field} ?? $defaults[$field]))
                       class="mt-1 w-5 h-5 rounded border-gray-300 text-[#FF7A1A] focus:ring-[#FF7A1A]">
                <div>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $label }}</div>
                    <div class="text-xs text-[#8A8F9C] mt-1">{{ $hint }}</div>
                </div>
            </label>
        @endforeach
    </div>
</div>

<div class="mt-6">
    <label class="block text-sm font-bold text-[#0A1128] mb-2">Cláusulas adicionais</label>
    <p class="text-xs text-[#8A8F9C] mb-2">Cláusulas específicas deste contrato (multa, rescisão, etc.). Aparecem no PDF após as cláusulas padrão.</p>
    <textarea name="clausulas_extras" rows="5" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">{{ old('clausulas_extras', $c->clausulas_extras ?? '') }}</textarea>
</div>

<div class="mt-6">
    <label class="block text-sm font-bold text-[#0A1128] mb-2">Corpo livre (opcional)</label>
    <p class="text-xs text-[#8A8F9C] mb-2">Caso prefira redigir o contrato inteiro à mão em vez de usar o template automático.</p>
    <textarea name="conteudo" rows="6" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128] font-mono text-sm">{{ old('conteudo', $c->conteudo ?? '') }}</textarea>
</div>

@isset($showStatus)
    <div class="mt-6">
        <label class="block text-sm font-bold text-[#0A1128] mb-2">Status do contrato</label>
        <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#0A1128] focus:ring-[#0A1128]">
            @foreach(['rascunho', 'enviado', 'assinado', 'encerrado', 'cancelado'] as $s)
                <option value="{{ $s }}" @selected(old('status', $c->status ?? 'rascunho') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
@endisset
