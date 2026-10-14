<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_bot_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('segment', 40);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_bot_profiles');
    }
};
