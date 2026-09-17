<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('logo_public_path')->nullable()->after('status');
            $table->boolean('exibir_home')->default(false)->after('logo_public_path');
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['logo_public_path', 'exibir_home']);
        });
    }
};
