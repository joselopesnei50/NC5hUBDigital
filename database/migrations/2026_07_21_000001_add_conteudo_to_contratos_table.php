<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('contratos', 'conteudo')) {
            Schema::table('contratos', function (Blueprint $table) {
                $table->longText('conteudo')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('contratos', 'conteudo')) {
            Schema::table('contratos', function (Blueprint $table) {
                $table->dropColumn('conteudo');
            });
        }
    }
};
