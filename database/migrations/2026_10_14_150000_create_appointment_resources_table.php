<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointment_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nome', 120);
            $table->string('descricao', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();

            $table->index(['cliente_id', 'is_active', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_resources');
    }
};
