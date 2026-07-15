<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-white leading-tight tracking-tight">
                Tickets de Suporte
            </h2>
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                Novo Chamado
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="bg-[#111827] border border-gray-800 rounded-2xl overflow-hidden">
            <div class="p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                <h3 class="text-xl font-medium text-white mb-2">Central de Ajuda</h3>
                <p>Nenhum chamado aberto no momento. Se precisar de assistência, inicie um novo atendimento.</p>
            </div>
        </div>
    </div>
</x-app-layout>
