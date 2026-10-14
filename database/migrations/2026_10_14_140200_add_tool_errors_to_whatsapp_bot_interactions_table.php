<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('whatsapp_bot_interactions', function (Blueprint $table) {
            $table->json('tool_errors')->nullable()->after('failed');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_bot_interactions', function (Blueprint $table) {
            $table->dropColumn('tool_errors');
        });
    }
};
