<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->unsignedSmallInteger('reminder_hours_before')->default(2)->after('cases_exemplo');
            $table->unsignedSmallInteger('duracao_padrao_atendimento_min')->default(60)->after('reminder_hours_before');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->dropColumn(['reminder_hours_before', 'duracao_padrao_atendimento_min']);
        });
    }
};
