<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nome');
            $table->string('cnpj_cpf', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('telefone', 30)->nullable();
            $table->string('endereco')->nullable();
            $table->string('categoria', 60)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'nome']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fornecedores');
    }
};
