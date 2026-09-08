<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\PedidoCliente::whereNull('token_publico')->get()->each(function($p) {
    $p->update(['token_publico' => \Illuminate\Support\Str::uuid()->toString()]);
});
echo "Done\n";
