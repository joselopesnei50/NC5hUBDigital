<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.materiais.index') }}" class="text-[#8A8F9C] hover:text-[#0A1128] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">{{ $material->titulo }}</h2>
            </div>
            <a href="{{ route('admin.materiais.edit', $material->id) }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">Editar</a>
        </div>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm max-w-3xl">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Cliente</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $material->cliente->razao_social ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Tipo</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0A1128] capitalize">{{ $material->tipo ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Enviado em</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0A1128]">{{ $material->created_at->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Status</dt>
                <dd class="mt-1">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase
                        {{ $material->status_aprovacao == 'aprovado' ? 'bg-emerald-100 text-emerald-700' : ($material->status_aprovacao == 'reprovado' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                        {{ $material->status_aprovacao }}
                    </span>
                </dd>
            </div>
            @if($material->arquivo_path)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold text-[#8A8F9C] uppercase tracking-wider">Arquivo</dt>
                    <dd class="mt-1">
                        <a href="{{ $material->arquivo_path }}" target="_blank" rel="noopener noreferrer" class="text-[#E63888] hover:text-[#0A1128] font-semibold text-sm break-all">{{ $material->arquivo_path }}</a>
                    </dd>
                </div>
            @endif
        </dl>
    </div>
</x-admin-layout>
