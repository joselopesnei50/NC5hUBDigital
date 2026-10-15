<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->string('resource_label', 40)->default('Profissional')->after('horario_comercial');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->dropColumn('resource_label');
        });
    }
};
