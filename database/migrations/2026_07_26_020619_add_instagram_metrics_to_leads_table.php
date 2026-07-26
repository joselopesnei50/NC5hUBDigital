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
            $table->integer('ig_followers')->nullable()->after('lcp_time');
            $table->integer('ig_posts')->nullable()->after('ig_followers');
            $table->text('ig_bio')->nullable()->after('ig_posts');
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
            $table->dropColumn(['ig_followers', 'ig_posts', 'ig_bio']);
        });
    }
};
