<x-admin-layout>
    <div class="max-w-[1600px] mx-auto">
        @include('customer.whatsapp._web_client', [
            'routePrefix' => 'admin.hub.whatsapp.',
            'instance' => $instance ?? null,
            'chatsData' => $chatsData ?? [],
            'activeChat' => $chat,
            'messages' => $messages ?? collect(),
        ])
    </div>
</x-admin-layout>
