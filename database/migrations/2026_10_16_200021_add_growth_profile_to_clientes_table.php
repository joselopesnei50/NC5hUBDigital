<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nicho_atuacao', 60)->nullable()->after('status');
            $table->string('tom_voz', 40)->nullable()->after('nicho_atuacao');
            $table->text('diferenciais_competitivos')->nullable()->after('tom_voz');
            $table->text('publico_alvo')->nullable()->after('diferenciais_competitivos');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'nicho_atuacao',
                'tom_voz',
                'diferenciais_competitivos',
                'publico_alvo',
            ]);
        });
    }
};
