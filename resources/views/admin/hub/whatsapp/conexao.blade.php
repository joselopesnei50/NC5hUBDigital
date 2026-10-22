<x-admin-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Hub NC5 · Atendimento</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">WhatsApp — Conexão (QR Code ou API Oficial)</h2>
            <p class="text-slate text-sm mt-1">Conecte o número da NC5 Hub Digital via QR Code (Evolution) ou pela API Oficial da Meta (Cloud API).</p>
        </div>
    </x-slot>

    @include('partials.whatsapp-conexao-content', ['routePrefix' => 'admin.hub.whatsapp.'])
</x-admin-layout>
