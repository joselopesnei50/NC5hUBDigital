<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contratos_prestadores', function (Blueprint $table) {
            $table->string('signature_token', 64)->nullable()->unique()->after('status_assinatura');
            $table->timestamp('enviado_em')->nullable()->after('signature_token');
        });
    }

    public function down()
    {
        Schema::table('contratos_prestadores', function (Blueprint $table) {
            $table->dropUnique(['signature_token']);
            $table->dropColumn(['signature_token', 'enviado_em']);
        });
    }
};
