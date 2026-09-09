<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Configurações do Agente por Tenant
        Schema::create('agent_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->integer('frequency_days')->default(7);
            $table->json('enabled_channels')->nullable();
            $table->decimal('cost_limit_brl', 8, 2)->default(50.00);
            $table->json('domains')->nullable();
            $table->string('tone')->default('profissional e direto');
            $table->timestamps();
        });

        // Histórico de Execuções e Auditoria de Custos do LLM
        Schema::create('agent_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('type')->default('scheduled'); // scheduled, on_demand, chat
            $table->string('status'); // enum AgentRunStatus
            $table->integer('period_days');
            $table->json('snapshot')->nullable();
            $table->integer('tokens_in')->default(0);
            $table->integer('tokens_out')->default(0);
            $table->decimal('cost', 10, 4)->default(0);
            $table->float('duration_seconds')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });

        // Insights Gerados (Diagnósticos)
        Schema::create('agent_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_run_id')->constrained('agent_runs')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('category'); // enum AgentInsightCategory
            $table->string('severity'); // enum AgentInsightSeverity
            $table->string('title');
            $table->text('body');
            $table->json('evidence')->nullable();
            $table->text('suggested_action')->nullable();
            $table->string('status')->default('novo'); // novo, lido, arquivado, aplicado
            $table->timestamps();
        });

        // Rascunhos de Notificações para o Cliente Final
        Schema::create('agent_notification_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('cliente_final_id')->nullable()->constrained('clientes_finais')->nullOnDelete();
            $table->string('trigger'); // ex: recompra, churn_alert
            $table->string('channel')->default('email');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('rascunho'); // enum AgentNotificationStatus
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_notification_drafts');
        Schema::dropIfExists('agent_insights');
        Schema::dropIfExists('agent_runs');
        Schema::dropIfExists('agent_settings');
    }
};
