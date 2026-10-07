<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('chat_id')->constrained('whatsapp_chats')->cascadeOnDelete();
            $table->string('wa_message_id', 100)->nullable();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('type', ['chat', 'image', 'video', 'audio', 'document', 'sticker', 'other'])->default('chat');
            $table->text('content')->nullable();
            $table->string('media_path')->nullable();
            $table->string('media_mimetype', 80)->nullable();
            $table->text('media_caption')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['cliente_id', 'chat_id', 'created_at']);
            $table->unique(['cliente_id', 'wa_message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
