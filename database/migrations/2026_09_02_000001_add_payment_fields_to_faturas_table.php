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
        Schema::table('faturas', function (Blueprint $table) {
            $table->string('nota_fiscal_path')->nullable()->after('link_pagamento');
            $table->string('forma_pagamento')->nullable()->after('nota_fiscal_path');
            $table->text('dados_bancarios')->nullable()->after('forma_pagamento');
            $table->string('comprovante_path')->nullable()->after('dados_bancarios');
            $table->timestamp('comprovante_enviado_em')->nullable()->after('comprovante_path');
            $table->text('observacoes')->nullable()->after('comprovante_enviado_em');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faturas', function (Blueprint $table) {
            $table->dropColumn([
                'nota_fiscal_path',
                'forma_pagamento',
                'dados_bancarios',
                'comprovante_path',
                'comprovante_enviado_em',
                'observacoes',
            ]);
        });
    }
};
