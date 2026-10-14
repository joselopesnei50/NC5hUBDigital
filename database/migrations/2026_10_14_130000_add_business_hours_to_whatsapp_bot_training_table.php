<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->json('horario_comercial')->nullable()->after('duracao_padrao_atendimento_min');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->dropColumn('horario_comercial');
        });
    }
};
