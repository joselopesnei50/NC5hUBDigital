<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela Principal das Salas de Chat (Conversas)
        Schema::create('agent_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->default('Nova Conversa com Bruce');
            $table->integer('last_turn')->default(0);
            $table->integer('accumulated_tokens')->default(0);
            $table->text('summary')->nullable(); // Resumo automático para poupar tokens
            $table->string('status')->default('active'); // active, archived
            $table->timestamps();
        });

        // Histórico de Mensagens de cada Chat
        Schema::create('agent_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('agent_conversations')->cascadeOnDelete();
            $table->string('role'); // user, assistant, tool, system
            $table->text('content')->nullable();
            $table->json('tool_calls')->nullable(); // Caso o assistente peça ferramentas
            $table->string('tool_call_id')->nullable(); // ID da ferramenta que rodamos
            $table->integer('tokens')->default(0);
            $table->decimal('cost', 10, 4)->default(0); // Auditoria de custo por mensagem
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_messages');
        Schema::dropIfExists('agent_conversations');
    }
};
