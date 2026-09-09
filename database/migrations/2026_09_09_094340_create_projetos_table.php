<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('pedido_cliente_id')->nullable()->constrained('pedidos_clientes')->onDelete('set null');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->enum('status', ['pendente', 'em_andamento', 'aguardando_cliente', 'concluido'])->default('pendente');
            $table->date('data_inicio')->nullable();
            $table->date('data_previsao')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projetos');
    }
};
