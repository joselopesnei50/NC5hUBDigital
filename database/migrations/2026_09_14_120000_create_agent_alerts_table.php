<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('tipo', 60);        // ex: 'caixa_negativo', 'top_cliente_sumido'
            $table->string('severidade', 20);  // 'info' | 'atencao' | 'critico'
            $table->string('titulo');
            $table->text('mensagem');
            $table->json('detalhes')->nullable();
            $table->timestamp('lido_em')->nullable();
            $table->timestamp('dispensado_em')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'dispensado_em']);
            $table->index(['cliente_id', 'tipo', 'dispensado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_alerts');
    }
};
