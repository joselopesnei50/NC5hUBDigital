<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('material_replies', function (Blueprint $table) {
            $table->string('anexo_link', 2048)->nullable()->after('anexo_path');
        });
    }

    public function down()
    {
        Schema::table('material_replies', function (Blueprint $table) {
            $table->dropColumn('anexo_link');
        });
    }
};
