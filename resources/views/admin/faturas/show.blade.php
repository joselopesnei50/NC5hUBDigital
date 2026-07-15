<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.faturas.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Fatura #{{ $fatura->id }}</h2>
                    <p class="text-sm text-[#8A8F9C] mt-1">{{ $fatura->descricao }}</p>
                </div>
            </div>
            <a href="{{ route('admin.faturas.edit', $fatura->id) }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">Editar</a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <h3 class="text-lg font-bold text-[#0A1128] mb-6">Dados da Cobrança</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Cliente</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $fatura->cliente->razao_social ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">E-mail</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $fatura->cliente->user->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Vencimento</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ \Carbon\Carbon::parse($fatura->vencimento)->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Emissão</dt>
                    <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $fatura->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-[#0A1128] rounded-2xl p-6 shadow-lg text-white">
            <p class="text-xs font-bold uppercase tracking-wider text-white/60">Valor</p>
            <p class="text-4xl font-extrabold mt-2">R$ {{ number_format($fatura->valor, 2, ',', '.') }}</p>
            <div class="mt-4">
                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase
                    {{ $fatura->status == 'pago' ? 'bg-emerald-500/20 text-emerald-300' : ($fatura->status == 'atrasado' ? 'bg-red-500/20 text-red-300' : 'bg-orange-500/20 text-orange-300') }}">
                    {{ $fatura->status }}
                </span>
            </div>
        </div>
    </div>
</x-admin-layout>
