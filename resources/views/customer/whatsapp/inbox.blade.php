<x-app-layout>
    @include('customer.whatsapp._web_client', [
        'routePrefix' => 'customer.whatsapp.',
        'instance' => $instance ?? null,
        'chatsData' => $chatsData ?? [],
        'activeChat' => null,
        'messages' => collect(),
    ])
</x-app-layout>
