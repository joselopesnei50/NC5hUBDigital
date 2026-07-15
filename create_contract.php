<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cliente = \App\Models\Cliente::first(); 
$servico = \App\Models\Servico::first(); 
if($cliente && $servico) { 
    \App\Models\Contrato::create([
        'cliente_id' => $cliente->id, 
        'servico_id' => $servico->id, 
        'data_inicio' => now(), 
        'data_fim' => now()->addYear(), 
        'status' => 'pendente', 
        'status_assinatura' => 'pendente'
    ]); 
    echo "Contrato pendente criado com sucesso!\n"; 
} else {
    echo "Cliente ou Servico nao encontrado.\n";
}
