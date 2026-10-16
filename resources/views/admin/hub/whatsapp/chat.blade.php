<x-admin-layout>
    @include('customer.whatsapp._web_client', [
        'routePrefix' => 'admin.hub.whatsapp.',
        'instance' => $instance ?? null,
        'chatsData' => $chatsData ?? [],
        'activeChat' => $chat,
        'messages' => $messages ?? collect(),
    ])
</x-admin-layout>
