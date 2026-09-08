<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('clientes_finais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('nome_empresa');
            $table->string('nome_responsavel')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('cidade')->nullable();
            $table->text('ultimos_pedidos')->nullable();
            $table->date('data_aniversario_empresa')->nullable();
            $table->date('data_aniversario_responsavel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes_finais');
    }
};
