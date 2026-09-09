<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lancamentos_financeiros', function (Blueprint $table) {
            $table->foreignId('cliente_final_id')->nullable()->constrained('clientes_finais')->onDelete('set null');
        });

        Schema::table('projetos', function (Blueprint $table) {
            $table->foreignId('cliente_final_id')->nullable()->constrained('clientes_finais')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('lancamentos_financeiros', function (Blueprint $table) {
            $table->dropForeign(['cliente_final_id']);
            $table->dropColumn('cliente_final_id');
        });

        Schema::table('projetos', function (Blueprint $table) {
            $table->dropForeign(['cliente_final_id']);
            $table->dropColumn('cliente_final_id');
        });
    }
};
