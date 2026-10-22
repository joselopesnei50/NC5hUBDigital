<?php

return [

    'meta_api_version' => env('META_GRAPH_VERSION', 'v22.0'),

    'evolution' => [
        'base_url' => rtrim(env('EVOLUTION_API_URL', ''), '/'),
        'global_key' => env('EVOLUTION_GLOBAL_KEY', ''),
        'webhook_secret' => env('EVOLUTION_WEBHOOK_SECRET', ''),
        'timeout' => (int) env('EVOLUTION_TIMEOUT', 15),
        'timeout_media' => (int) env('EVOLUTION_TIMEOUT_MEDIA', 45),
    ],

    'meta_cloud' => [
        'app_id' => env('META_APP_ID', ''),
        'app_secret' => env('META_APP_SECRET', ''),
        'config_id' => env('META_CLOUD_CONFIG_ID', ''),
        'verify_token' => env('META_WEBHOOK_VERIFY_TOKEN', ''),
        'forward_url' => env('META_WEBHOOK_FORWARD_URL', ''),
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
