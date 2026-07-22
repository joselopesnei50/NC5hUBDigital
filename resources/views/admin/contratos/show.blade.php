<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.contratos.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Contrato #{{ $contrato->id }}</h2>
                    <p class="text-sm text-[#8A8F9C] mt-1">{{ $contrato->servico->nome ?? 'Contrato Avulso' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.contratos.pdf', $contrato->id) }}" target="_blank"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Baixar PDF
                </a>
                <a href="{{ route('admin.contratos.edit', $contrato->id) }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">Editar</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <h3 class="text-lg font-bold text-[#0A1128] mb-6">Dados do Contrato</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Cliente</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $contrato->cliente->razao_social ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Serviço</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $contrato->servico->nome ?? 'Contrato Avulso' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Início</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Término</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $contrato->data_fim ? \Carbon\Carbon::parse($contrato->data_fim)->format('d/m/Y') : 'Vigente' }}</dd>
                </div>
                @if($contrato->servico)
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Valor</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">R$ {{ number_format($contrato->servico->preco, 2, ',', '.') }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Assinatura</dt>
                    <dd class="mt-1">
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ ($contrato->status_assinatura ?? '') == 'assinado' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $contrato->status_assinatura ?? 'pendente' }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-bold text-[#8A8F9C] uppercase tracking-wider mb-2">Status</h3>
            <p class="text-3xl font-extrabold text-[#0A1128] capitalize">{{ $contrato->status }}</p>
            <p class="text-xs text-[#8A8F9C] mt-2">Criado em {{ $contrato->created_at->format('d/m/Y') }}</p>

            @if($contrato->assinatura_url)
                @php $sig = json_decode($contrato->assinatura_url, true); @endphp
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <p class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider mb-2">Registro de Assinatura</p>
                    <p class="text-[11px] text-[#0A1128]">{{ $sig['timestamp'] ?? '' }}</p>
                    <p class="text-[11px] text-[#8A8F9C]">IP: {{ $sig['ip'] ?? '' }}</p>
                    @if(!empty($sig['signature_image']))
                        <div class="mt-3 border border-gray-200 rounded-xl overflow-hidden bg-white p-2">
                            <img src="{{ $sig['signature_image'] }}" alt="Assinatura" class="w-full">
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if($contrato->conteudo)
        <div class="mt-6 bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <h3 class="text-lg font-bold text-[#0A1128] mb-6">Corpo do Contrato</h3>
            <div class="prose prose-sm max-w-none text-[#0A1128] leading-relaxed ql-editor" style="padding: 0;">
                {!! $contrato->conteudo !!}
            </div>
        </div>
    @else
        <div class="mt-6 bg-white border border-dashed border-gray-300 rounded-2xl p-8 text-center">
            <p class="text-sm text-[#8A8F9C]">Nenhum corpo de contrato redigido. <a href="{{ route('admin.contratos.edit', $contrato->id) }}" class="font-bold text-[#0A1128] hover:text-[#FF7A1A]">Editar para adicionar</a>.</p>
        </div>
    @endif
</x-admin-layout>
