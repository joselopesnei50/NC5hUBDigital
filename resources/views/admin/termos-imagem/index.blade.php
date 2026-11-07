<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">Termos de Uso de Imagem e Voz</h2>
                <p class="text-sm text-[#8A8F9C] mt-1">Autorizações de uso de imagem e voz para produções audiovisuais e fotográficas da NC5 e de seus clientes.</p>
            </div>
            <a href="{{ route('admin.termos-imagem.create') }}" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg flex items-center gap-2 w-fit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo Termo
            </a>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 flex-wrap">
            @foreach(['' => 'Todos', 'rascunho' => 'Rascunho', 'enviado' => 'Enviado', 'assinado' => 'Assinado', 'cancelado' => 'Cancelado'] as $v => $l)
                <a href="{{ route('admin.termos-imagem.index', array_filter(['status' => $v ?: null, 'cliente_id' => $clienteId ?: null])) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-bold border transition-colors {{ ($status ?? '') === $v ? 'bg-[#0A1128] text-white border-[#0A1128]' : 'bg-white text-[#8A8F9C] border-gray-200 hover:border-[#0A1128]' }}">
                    {{ $l }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.termos-imagem.index') }}" class="flex items-center gap-2">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <select name="cliente_id" onchange="this.form.submit()" class="rounded-xl border-gray-300 text-xs font-semibold text-[#0A1128] focus:border-[#0A1128] focus:ring-[#0A1128]">
                <option value="">Todos os clientes / NC5</option>
                <option value="nc5" @selected(($clienteId ?? '') === 'nc5')>Apenas NC5 Hub Digital (Interno)</option>
                @foreach($clientes as $cli)
                    <option value="{{ $cli->id }}" @selected((string) ($clienteId ?? '') === (string) $cli->id)>
                        {{ $cli->razao_social }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Título / Projeto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Cedente</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Cliente</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Captação</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($termos as $termo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-[#0A1128]">{{ $termo->titulo }}</div>
                                @if($termo->projeto_referencia)
                                    <div class="text-xs text-[#8A8F9C]">{{ $termo->projeto_referencia }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-[#0A1128]">
                                    {{ $termo->cedente_nome }}
                                    @if($termo->cedente_menor)
                                        <span class="ml-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700 uppercase">Menor</span>
                                    @endif
                                </div>
                                <div class="text-xs text-[#8A8F9C]">{{ $termo->cedente_cpf }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">
                                {{ $termo->cliente->razao_social ?? 'NC5 Hub Digital' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-[#8A8F9C]">
                                {{ $termo->data_captacao?->format('d/m/Y') ?? '—' }}
                                @if($termo->local_captacao)
                                    <div class="truncate max-w-[160px]">{{ $termo->local_captacao }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = [
                                        'rascunho'  => 'bg-gray-100 text-gray-700',
                                        'enviado'   => 'bg-amber-100 text-amber-700',
                                        'assinado'  => 'bg-emerald-100 text-emerald-700',
                                        'cancelado' => 'bg-rose-100 text-rose-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $colors[$termo->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $termo->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('admin.termos-imagem.pdf', $termo) }}" class="text-[#FF7A1A] hover:underline font-semibold text-sm">PDF</a>
                                    @if(!$termo->isAssinado())
                                        <a href="{{ route('admin.termos-imagem.edit', $termo) }}" class="text-[#8A8F9C] hover:text-[#FF7A1A] font-semibold text-sm transition-colors">Editar</a>
                                    @endif
                                    <a href="{{ route('admin.termos-imagem.show', $termo) }}" class="text-[#0A1128] hover:text-[#FF7A1A] font-bold text-sm transition-colors">Abrir</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-medium text-[#0A1128]">Nenhum termo de uso de imagem cadastrado.</p>
                                <a href="{{ route('admin.termos-imagem.create') }}" class="inline-block mt-3 text-[#FF7A1A] font-bold text-sm hover:underline">Criar o primeiro termo</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $termos->links() }}
        </div>
    </div>
</x-admin-layout>
