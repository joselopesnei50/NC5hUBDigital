<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('reminder_second_sent_at')->nullable()->after('reminder_sent_at');
            $table->timestamp('cancelled_at')->nullable()->after('reminder_second_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['reminder_second_sent_at', 'cancelled_at']);
        });
    }
};
