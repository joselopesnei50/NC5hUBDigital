<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('produtos_servicos', function (Blueprint $table) {
            $table->string('unidade_medida', 50)->nullable()->after('preco_padrao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos_servicos', function (Blueprint $table) {
            $table->dropColumn('unidade_medida');
        });
    }
};
