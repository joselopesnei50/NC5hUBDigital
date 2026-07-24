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
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('titulo');
            $table->string('anexo_admin_path')->nullable()->after('arquivo_path');
            $table->string('anexo_cliente_path')->nullable()->after('comentario_cliente');
            $table->timestamp('data_resposta')->nullable()->after('status_aprovacao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'descricao',
                'anexo_admin_path',
                'anexo_cliente_path',
                'data_resposta'
            ]);
        });
    }
};
