<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento Multicanal</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">WhatsApp — Conexão (QR Code ou API Oficial)</h2>
            <p class="text-slate text-sm mt-1">Conecte seu WhatsApp via leitura de QR Code ou pela API Oficial da Meta (Cloud API) para atendimento humano e com a BruceIA.</p>
        </div>
    </x-slot>

    @include('partials.whatsapp-conexao-content', ['routePrefix' => 'customer.whatsapp.'])
</x-app-layout>
