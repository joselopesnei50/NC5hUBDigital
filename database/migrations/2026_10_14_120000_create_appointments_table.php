<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('cliente_final_id')->nullable()->constrained('clientes_finais')->nullOnDelete();
            $table->foreignId('chat_id')->nullable()->constrained('whatsapp_chats')->nullOnDelete();
            $table->string('titulo', 160);
            $table->text('descricao')->nullable();
            $table->string('contato_nome', 160)->nullable();
            $table->string('contato_telefone', 30)->nullable();
            $table->timestamp('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->string('status', 20)->default('agendado');
            $table->timestamp('reminder_sent_at')->nullable();
            $table->string('created_via', 20)->default('manual');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'scheduled_at']);
            $table->index(['status', 'reminder_sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
