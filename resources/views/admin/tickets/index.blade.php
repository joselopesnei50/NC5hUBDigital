<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
            Suporte & Tickets
        </h2>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Assunto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Cliente</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Abertura</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#0A1128]">{{ $ticket->assunto }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $ticket->cliente->razao_social ?? 'Cliente Deletado' }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $ticket->status == 'aberto' ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700' }} uppercase">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="text-[#8A8F9C] hover:text-[#FF7A1A] font-semibold text-sm transition-colors">Responder</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#8A8F9C]">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                <p class="font-medium text-[#0A1128]">Caixa de entrada limpa.</p>
                                <p class="text-sm mt-1">Nenhum chamado de suporte aberto pelos clientes no momento.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
    </div>
</x-admin-layout>
