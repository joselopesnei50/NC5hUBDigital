<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('cliente_final_id')->nullable()->constrained('clientes_finais')->nullOnDelete();
            $table->string('wa_id', 60);
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->boolean('is_group')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamps();

            $table->unique(['cliente_id', 'wa_id']);
            $table->index(['cliente_id', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_chats');
    }
};
