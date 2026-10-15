<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('resource_id')->nullable()->after('chat_id')->constrained('appointment_resources')->nullOnDelete();
            $table->index(['cliente_id', 'resource_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['resource_id']);
            $table->dropIndex(['cliente_id', 'resource_id', 'scheduled_at']);
            $table->dropColumn('resource_id');
        });
    }
};
