<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->boolean('enable_second_reminder')->default(false)->after('reminder_hours_before');
            $table->unsignedSmallInteger('reminder_hours_before_second')->default(24)->after('enable_second_reminder');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_bot_training', function (Blueprint $table) {
            $table->dropColumn(['enable_second_reminder', 'reminder_hours_before_second']);
        });
    }
};
