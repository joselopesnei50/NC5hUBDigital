<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_bot_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('chat_id')->constrained('whatsapp_chats')->cascadeOnDelete();
            $table->string('inbound_message_id', 120)->nullable();
            $table->string('outbound_message_id', 120)->nullable();
            $table->unsignedInteger('tokens_in')->default(0);
            $table->unsignedInteger('tokens_out')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->unsignedInteger('response_time_ms')->default(0);
            $table->boolean('escalated')->default(false);
            $table->boolean('failed')->default(false);
            $table->timestamps();

            $table->index(['cliente_id', 'created_at']);
            $table->index(['chat_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_bot_interactions');
    }
};
