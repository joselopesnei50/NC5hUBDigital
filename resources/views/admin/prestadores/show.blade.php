<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">{{ $prestador->nome }}</h2>
                <p class="text-sm text-[#8A8F9C] mt-1">{{ $prestador->tipo_pessoa === 'PJ' ? 'Pessoa Jurídica' : 'Pessoa Física' }} · {{ $prestador->categoria ?: 'Sem categoria' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.contratos-prestadores.create', ['prestador_id' => $prestador->id]) }}" class="bg-[#FF7A1A] hover:bg-[#E5651A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">+ Novo contrato</a>
                <a href="{{ route('admin.prestadores.edit', $prestador) }}" class="bg-white border border-gray-300 hover:border-[#0A1128] text-[#0A1128] px-5 py-2.5 rounded-xl text-sm font-bold transition-colors">Editar</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest">Dados cadastrais</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">CPF/CNPJ</dt><dd class="text-[#0A1128] font-bold">{{ $prestador->cpf_cnpj ?: '—' }}</dd></div>
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">RG/IE</dt><dd class="text-[#0A1128]">{{ $prestador->rg_ie ?: '—' }}</dd></div>
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">E-mail</dt><dd class="text-[#0A1128]">{{ $prestador->email ?: '—' }}</dd></div>
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">Telefone</dt><dd class="text-[#0A1128]">{{ $prestador->telefone ?: '—' }}</dd></div>
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">Endereço</dt><dd class="text-[#0A1128]">{{ $prestador->endereco ?: '—' }}{{ $prestador->cidade ? ' · ' . $prestador->cidade : '' }}{{ $prestador->uf ? '/' . $prestador->uf : '' }}</dd></div>
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">Status</dt><dd><span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $prestador->ativo ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $prestador->ativo ? 'Ativo' : 'Inativo' }}</span></dd></div>
            </dl>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest">Dados bancários</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">Banco</dt><dd class="text-[#0A1128] font-bold">{{ $prestador->banco ?: '—' }}</dd></div>
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">Agência / Conta</dt><dd class="text-[#0A1128]">{{ $prestador->agencia ?: '—' }} / {{ $prestador->conta ?: '—' }}</dd></div>
                <div><dt class="text-xs text-[#8A8F9C] font-semibold uppercase">Chave PIX</dt><dd class="text-[#0A1128]">{{ $prestador->chave_pix ?: '—' }}</dd></div>
            </dl>
            @if($prestador->notas)
                <div class="pt-4 border-t border-gray-100">
                    <dt class="text-xs text-[#8A8F9C] font-semibold uppercase mb-1">Notas internas</dt>
                    <dd class="text-sm text-[#0A1128] whitespace-pre-line">{{ $prestador->notas }}</dd>
                </div>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold text-[#8A8F9C] uppercase tracking-widest">Contratos</h3>
                <span class="text-xs font-bold text-[#0A1128]">{{ $prestador->contratos->count() }}</span>
            </div>
            @forelse($prestador->contratos as $contrato)
                <a href="{{ route('admin.contratos-prestadores.show', $contrato) }}" class="block mb-3 p-3 rounded-xl border border-gray-200 hover:border-[#FF7A1A] transition-colors">
                    <div class="font-bold text-sm text-[#0A1128]">{{ $contrato->titulo }}</div>
                    <div class="text-xs text-[#8A8F9C] mt-1">R$ {{ number_format($contrato->valor, 2, ',', '.') }} · {{ $contrato->status }}</div>
                </a>
            @empty
                <p class="text-sm text-[#8A8F9C]">Nenhum contrato gerado ainda.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
