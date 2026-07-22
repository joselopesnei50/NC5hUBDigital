<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE contratos MODIFY COLUMN assinatura_url LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE contratos MODIFY COLUMN assinatura_url VARCHAR(255) NULL');
    }
};
