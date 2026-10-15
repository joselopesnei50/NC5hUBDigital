<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointment_resources', function (Blueprint $table) {
            $table->boolean('use_custom_hours')->default(false)->after('ordem');
            $table->json('horario_comercial')->nullable()->after('use_custom_hours');
        });
    }

    public function down(): void
    {
        Schema::table('appointment_resources', function (Blueprint $table) {
            $table->dropColumn(['use_custom_hours', 'horario_comercial']);
        });
    }
};
