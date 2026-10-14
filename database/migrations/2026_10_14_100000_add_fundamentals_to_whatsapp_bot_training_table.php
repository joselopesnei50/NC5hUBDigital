<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->text('processo_producao')->nullable()->after('regras_proibicoes');
            $table->text('diferenciais')->nullable()->after('processo_producao');
            $table->text('historia_negocio')->nullable()->after('diferenciais');
            $table->text('publico_alvo')->nullable()->after('historia_negocio');
            $table->string('tom_de_voz', 500)->nullable()->after('publico_alvo');
            $table->text('politicas')->nullable()->after('tom_de_voz');
            $table->text('cases_exemplo')->nullable()->after('politicas');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->dropColumn([
                'processo_producao',
                'diferenciais',
                'historia_negocio',
                'publico_alvo',
                'tom_de_voz',
                'politicas',
                'cases_exemplo',
            ]);
        });
    }
};
