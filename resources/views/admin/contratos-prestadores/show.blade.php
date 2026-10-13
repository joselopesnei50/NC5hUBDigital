<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">{{ $contrato->titulo }}</h2>
                <p class="text-sm text-[#8A8F9C] mt-1">Prestador: <strong>{{ $contrato->prestador->nome ?? '—' }}</strong> · R$ {{ number_format($contrato->valor, 2, ',', '.') }} · {{ $contrato->forma_pagamento_label }}</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                @if($contrato->status_assinatura !== 'assinado')
                    <form method="POST" action="{{ route('admin.contratos-prestadores.enviar-email', $contrato) }}"
                          onsubmit="return confirm('Enviar contrato para o e-mail do prestador ({{ $contrato->prestador->email ?? 'sem e-mail' }})?')">
                        @csrf
                        <button type="submit"
                                @disabled(empty($contrato->prestador->email))
                                class="bg-[#0A1128] hover:bg-[#FF7A1A] disabled:opacity-40 disabled:cursor-not-allowed text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                            {{ $contrato->enviado_em ? 'Reenviar e-mail' : 'Enviar e-mail de assinatura' }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.contratos-prestadores.pdf', $contrato) }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">Baixar PDF</a>
                <a href="{{ route('admin.contratos-prestadores.edit', $contrato) }}" class="bg-white border border-gray-300 hover:border-[#0A1128] text-[#0A1128] px-5 py-2.5 rounded-xl text-sm font-bold transition-colors">Editar</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm p-8 space-y-6">
            <div>
                <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-2">Objeto</h3>
                <p class="text-sm text-[#0A1128] whitespace-pre-line">{{ $contrato->objeto }}</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-100">
                <div>
                    <div class="text-xs text-[#8A8F9C] font-semibold uppercase">Início</div>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $contrato->data_inicio?->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div class="text-xs text-[#8A8F9C] font-semibold uppercase">Fim</div>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $contrato->data_fim?->format('d/m/Y') ?? 'Indeterminado' }}</div>
                </div>
                <div>
                    <div class="text-xs text-[#8A8F9C] font-semibold uppercase">Pagamento</div>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $contrato->forma_pagamento_label }}</div>
                </div>
                <div>
                    <div class="text-xs text-[#8A8F9C] font-semibold uppercase">Prazo de entrega</div>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $contrato->prazo_entrega ?: '—' }}</div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-3">Cláusulas padrão</h3>
                <ul class="space-y-2 text-sm">
                    @foreach([
                        'clausula_confidencialidade' => 'Confidencialidade (NDA)',
                        'clausula_propriedade_intelectual' => 'Propriedade intelectual',
                        'clausula_nao_vinculo_empregaticio' => 'Não vínculo empregatício',
                        'clausula_exclusividade' => 'Exclusividade',
                    ] as $field => $label)
                        <li class="flex items-center gap-2">
                            <span class="w-4 h-4 flex items-center justify-center rounded-full {{ $contrato->$field ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }} text-[10px] font-bold">
                                {{ $contrato->$field ? '✓' : '×' }}
                            </span>
                            <span class="{{ $contrato->$field ? 'text-[#0A1128]' : 'text-[#8A8F9C] line-through' }}">{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if($contrato->clausulas_extras)
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-2">Cláusulas adicionais</h3>
                    <p class="text-sm text-[#0A1128] whitespace-pre-line">{{ $contrato->clausulas_extras }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest">Status</h3>
            @php
                $colors = [
                    'rascunho' => 'bg-gray-100 text-gray-700',
                    'enviado' => 'bg-amber-100 text-amber-700',
                    'assinado' => 'bg-emerald-100 text-emerald-700',
                    'cancelado' => 'bg-rose-100 text-rose-700',
                    'encerrado' => 'bg-slate-200 text-slate-700',
                ];
            @endphp
            <span class="inline-block px-3 py-1.5 text-xs font-bold rounded-full uppercase {{ $colors[$contrato->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $contrato->status }}
            </span>

            @if($contrato->enviado_em)
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-1">E-mail enviado em</h3>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $contrato->enviado_em->format('d/m/Y H:i') }}</div>
                </div>
            @endif

            @if($contrato->signature_token)
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-2">Link público de assinatura</h3>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="{{ route('public.contrato-prestador.show', $contrato->signature_token) }}"
                               class="flex-1 text-xs rounded-lg border-gray-200 bg-gray-50 text-[#0A1128]" onclick="this.select()">
                        <button type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='✓'" class="text-xs font-bold bg-[#0A1128] text-white px-3 py-2 rounded-lg">Copiar</button>
                    </div>
                </div>
            @endif

            @if($contrato->status_assinatura === 'assinado' && $contrato->assinado_em)
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-1">Assinado em</h3>
                    <div class="text-sm font-bold text-[#0A1128]">{{ $contrato->assinado_em->format('d/m/Y H:i') }}</div>
                    @php $sig = json_decode($contrato->assinatura_url ?? '{}', true) ?: []; @endphp
                    @if(!empty($sig['nome']))
                        <div class="text-xs text-[#8A8F9C] mt-2">
                            <strong>{{ $sig['nome'] }}</strong> · {{ $sig['documento'] ?? '' }}<br>
                            IP: {{ $sig['ip'] ?? '—' }}
                        </div>
                    @endif
                </div>
            @endif

            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest mb-2">Prestador</h3>
                <a href="{{ route('admin.prestadores.show', $contrato->prestador) }}" class="block text-sm font-bold text-[#0A1128] hover:text-[#FF7A1A]">
                    {{ $contrato->prestador->nome ?? '—' }}
                </a>
                <div class="text-xs text-[#8A8F9C] mt-1">{{ $contrato->prestador->cpf_cnpj ?? '' }}</div>
                <div class="text-xs text-[#8A8F9C]">{{ $contrato->prestador->email ?? '' }}</div>
            </div>
        </div>
    </div>
</x-admin-layout>
