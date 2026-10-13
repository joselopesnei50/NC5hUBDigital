<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Contratos com Prestadores</h2>
                <p class="text-sm text-[#8A8F9C] mt-1">Contratos de prestação de serviço com freelancers e parceiros NC5.</p>
            </div>
            <a href="{{ route('admin.contratos-prestadores.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg flex items-center gap-2 w-fit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo Contrato
            </a>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-2 flex-wrap">
        @foreach(['' => 'Todos', 'rascunho' => 'Rascunho', 'enviado' => 'Enviado', 'assinado' => 'Assinado', 'encerrado' => 'Encerrado', 'cancelado' => 'Cancelado'] as $v => $l)
            <a href="{{ route('admin.contratos-prestadores.index', $v ? ['status' => $v] : []) }}"
               class="px-3 py-1.5 rounded-full text-xs font-bold border transition-colors {{ ($status ?? '') === $v ? 'bg-[#0A1128] text-white border-[#0A1128]' : 'bg-white text-[#8A8F9C] border-gray-200 hover:border-[#0A1128]' }}">
                {{ $l }}
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Título</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Prestador</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Valor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Vigência</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contratos as $contrato)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#0A1128]">{{ $contrato->titulo }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $contrato->prestador->nome ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-[#0A1128]">R$ {{ number_format($contrato->valor, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-xs text-[#8A8F9C]">
                                {{ $contrato->data_inicio?->format('d/m/Y') }}
                                @if($contrato->data_fim)
                                    → {{ $contrato->data_fim->format('d/m/Y') }}
                                @else
                                    → vigente
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = [
                                        'rascunho' => 'bg-gray-100 text-gray-700',
                                        'enviado' => 'bg-amber-100 text-amber-700',
                                        'assinado' => 'bg-emerald-100 text-emerald-700',
                                        'cancelado' => 'bg-rose-100 text-rose-700',
                                        'encerrado' => 'bg-slate-200 text-slate-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $colors[$contrato->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $contrato->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.contratos-prestadores.pdf', $contrato) }}" class="text-[#FF7A1A] hover:underline font-semibold text-sm">PDF</a>
                                    <a href="{{ route('admin.contratos-prestadores.edit', $contrato) }}" class="text-[#8A8F9C] hover:text-[#FF7A1A] font-semibold text-sm transition-colors">Editar</a>
                                    <a href="{{ route('admin.contratos-prestadores.show', $contrato) }}" class="text-[#0A1128] hover:text-[#FF7A1A] font-bold text-sm transition-colors">Abrir</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-medium text-[#0A1128]">Nenhum contrato gerado.</p>
                                <a href="{{ route('admin.contratos-prestadores.create') }}" class="inline-block mt-3 text-[#FF7A1A] font-bold text-sm hover:underline">Criar o primeiro</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $contratos->links() }}
        </div>
    </div>
</x-admin-layout>
