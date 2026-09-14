<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_knowledge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('conteudo');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['cliente_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_knowledge');
    }
};
