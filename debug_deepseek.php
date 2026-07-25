<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$key = \App\Models\Configuracao::get('deepseek_api_key');

$systemPrompt = "You are BruceIA.";
$userPrompt = "Analyze this.";

$response = Illuminate\Support\Facades\Http::withToken($key)
    ->post('https://api.deepseek.com/chat/completions', [
        'model' => 'deepseek-v4-flash',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userPrompt],
        ],
        'temperature' => 0.7,
    ]);

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
