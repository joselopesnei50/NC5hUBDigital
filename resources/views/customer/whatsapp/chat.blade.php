<x-app-layout>
    @include('customer.whatsapp._web_client', [
        'routePrefix' => 'customer.whatsapp.',
        'instance' => $instance ?? null,
        'chatsData' => $chatsData ?? [],
        'activeChat' => $chat,
        'messages' => $messages ?? collect(),
    ])
</x-app-layout>
