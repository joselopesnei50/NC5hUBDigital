<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $l = \App\Models\LancamentoFinanceiro::create([
        'tipo' => 'receber',
        'descricao' => 'Teste',
        'valor' => 100.50,
        'data_vencimento' => '2026-10-10',
        'cliente_id' => 1
    ]);
    echo "OK: " . $l->id;
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
