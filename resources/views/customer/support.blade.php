<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data="{ modalTicket: false }">
            <h2 class="font-bold text-2xl text-[#0A1128] leading-tight tracking-tight">
                Central de Suporte
            </h2>
            <button @click="modalTicket = true" class="bg-[#0A1128] hover:bg-[#FF7A1A] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-lg">
                Abrir Novo Chamado
            </button>

            <!-- Modal Novo Chamado -->
            <div x-show="modalTicket" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="modalTicket" x-transition class="fixed inset-0 bg-[#0A1128] bg-opacity-75 transition-opacity" @click="modalTicket = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-show="modalTicket" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                        <form action="{{ route('customer.support.store') }}" method="POST" class="p-8">
                            @csrf
                            <h3 class="text-2xl font-bold text-[#0A1128] mb-6">Abrir Chamado</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-[#0A1128] mb-1">Assunto</label>
                                    <input type="text" name="assunto" required class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Do que você precisa de ajuda?">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-[#0A1128] mb-1">Sua Mensagem</label>
                                    <textarea name="mensagem" rows="5" required class="w-full rounded-xl border-gray-300 focus:border-[#0A1128] focus:ring-[#0A1128]" placeholder="Descreva com detalhes..."></textarea>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="modalTicket = false" class="px-6 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-bold text-[#0A1128] hover:bg-gray-50">Cancelar</button>
                                <button type="submit" class="px-6 py-2.5 bg-[#0A1128] text-white rounded-xl text-sm font-bold hover:bg-[#FF7A1A] transition-colors shadow-lg">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#F4F5F7] border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Assunto do Chamado</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Abertura</th>
                        <th class="px-6 py-4 text-xs font-semibold text-[#8A8F9C] uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('customer.support.show', $ticket->id) }}'">
                            <td class="px-6 py-4 font-bold text-[#0A1128]">{{ $ticket->assunto }}</td>
                            <td class="px-6 py-4 text-sm text-[#8A8F9C]">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $ticket->status == 'aberto' ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700' }} uppercase">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-[#8A8F9C]">
                                <p class="font-medium text-[#0A1128]">Nenhum chamado de suporte.</p>
                                <p class="text-sm mt-1">Sempre que precisar, estamos aqui para ajudar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
