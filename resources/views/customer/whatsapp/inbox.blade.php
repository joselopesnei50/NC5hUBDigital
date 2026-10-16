<x-app-layout>
    <div class="py-3 bg-[#E9EDEF]/60 min-h-[calc(100vh-65px)]">
        <div class="max-w-[1600px] mx-auto px-2 sm:px-4 lg:px-6">
            @include('customer.whatsapp._web_client', [
                'routePrefix' => 'customer.whatsapp.',
                'instance' => $instance ?? null,
                'chatsData' => $chatsData ?? [],
                'activeChat' => null,
                'messages' => collect(),
            ])
        </div>
    </div>
</x-app-layout>
