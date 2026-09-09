<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    $user = App\Models\User::first();
    Auth::login($user);
    $c = app(Livewire\LivewireManager::class)->test(\App\Http\Livewire\BruceChat::class);
    echo "OK";
} catch (\Throwable $e) {
    echo $e->getMessage();
}
