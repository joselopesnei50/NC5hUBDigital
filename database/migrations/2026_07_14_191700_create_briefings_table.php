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
    public function up()
    {
        Schema::create('briefings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('anexo_admin_path')->nullable();
            
            $table->text('resposta_cliente')->nullable();
            $table->string('anexo_cliente_path')->nullable();
            
            $table->enum('status', ['pendente', 'respondido'])->default('pendente');
            
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
        Schema::dropIfExists('briefings');
    }
};
