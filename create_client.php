<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::firstOrCreate(
    ['email' => 'cliente@nc5.com.br'],
    ['name' => 'Cliente Teste', 'password' => bcrypt('cliente123'), 'role' => 'cliente']
);

\App\Models\Cliente::firstOrCreate(
    ['user_id' => $user->id],
    ['razao_social' => 'Empresa do Cliente', 'cpf_cnpj' => '11.111.111/0001-11', 'telefone' => '11999999999']
);

echo "Conta Cliente criada com sucesso!\n";
