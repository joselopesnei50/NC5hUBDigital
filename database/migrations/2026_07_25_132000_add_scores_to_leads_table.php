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
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('seo_score')->nullable()->after('resultado_ia');
            $table->integer('performance_score')->nullable()->after('seo_score');
            $table->integer('mobile_score')->nullable()->after('performance_score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['seo_score', 'performance_score', 'mobile_score']);
        });
    }
};
