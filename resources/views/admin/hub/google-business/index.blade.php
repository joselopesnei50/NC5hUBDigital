<x-admin-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Hub NC5 · Integração</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Google Meu Negócio</h2>
            <p class="text-slate text-sm mt-1">Gerencie a ficha do Google Meu Negócio da NC5, edite informações, publique posts e responda avaliações direto no painel administrativo.</p>
        </div>
    </x-slot>

    @include('customer.google-business._content', ['routePrefix' => 'admin.hub.google-business.'])
</x-admin-layout>
