<?php

return [

    'evolution' => [
        'base_url' => rtrim(env('EVOLUTION_API_URL', ''), '/'),
        'global_key' => env('EVOLUTION_GLOBAL_KEY', ''),
        'webhook_secret' => env('EVOLUTION_WEBHOOK_SECRET', ''),
        'timeout' => (int) env('EVOLUTION_TIMEOUT', 15),
        'timeout_media' => (int) env('EVOLUTION_TIMEOUT_MEDIA', 45),
    ],

    'webhook' => [
        'events' => [
            'QRCODE_UPDATED',
            'CONNECTION_UPDATE',
            'MESSAGES_UPSERT',
            'MESSAGES_UPDATE',
            'SEND_MESSAGE',
        ],
    ],

];
