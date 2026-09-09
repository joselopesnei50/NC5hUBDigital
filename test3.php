<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::first();
Auth::login($user);
try {
    view('customer.index', [
        'cliente' => App\Models\Cliente::first(),
        'faturasPendentes' => 0,
        'materiaisAguardando' => 0,
        'contratosPendentes' => 0
    ])->render();
    echo "OK";
} catch (\Throwable $e) {
    echo $e->getMessage();
}
