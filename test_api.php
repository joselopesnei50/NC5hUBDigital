<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$key = \App\Models\Configuracao::get('deepseek_api_key');
echo "Key: " . substr($key, 0, 5) . "...\n";

$response = Illuminate\Support\Facades\Http::withToken($key)
    ->post('https://api.deepseek.com/chat/completions', [
        'model' => 'deepseek-v4-flash',
        'messages' => [
            ['role' => 'user', 'content' => 'oi']
        ]
    ]);

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
