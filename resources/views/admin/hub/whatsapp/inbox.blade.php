<x-admin-layout>
    @include('customer.whatsapp._web_client', [
        'routePrefix' => 'admin.hub.whatsapp.',
        'instance' => $instance ?? null,
        'chatsData' => $chatsData ?? [],
        'activeChat' => null,
        'messages' => collect(),
    ])
</x-admin-layout>
