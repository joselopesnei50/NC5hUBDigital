<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('instance_name', 100)->unique();
            $table->string('instance_token', 64)->unique();
            $table->enum('status', ['open', 'connecting', 'close', 'paused'])->default('close');
            $table->string('phone_number', 20)->nullable();
            $table->string('owner_jid', 60)->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_status_check_at')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_instances');
    }
};
