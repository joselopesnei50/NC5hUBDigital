<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_bot_training', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->string('horario_atendimento', 255)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->text('faq_custom')->nullable();
            $table->text('catalogo_texto')->nullable();
            $table->text('regras_proibicoes')->nullable();
            $table->timestamps();

            $table->unique('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_bot_training');
    }
};
