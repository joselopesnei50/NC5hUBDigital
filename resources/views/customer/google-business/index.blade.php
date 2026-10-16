<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Integração</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Google Meu Negócio</h2>
            <p class="text-slate text-sm mt-1">Conecte sua ficha do Google para gerenciar postagens, informações e desempenho aqui no painel.</p>
        </div>
    </x-slot>

    @include('customer.google-business._content', ['routePrefix' => 'customer.google-business.'])
</x-app-layout>
