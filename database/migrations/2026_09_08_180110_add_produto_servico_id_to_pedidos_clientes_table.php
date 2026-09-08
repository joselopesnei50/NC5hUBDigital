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
    public function up(): void
    {
        Schema::table('pedidos_clientes', function (Blueprint $table) {
            $table->foreignId('produto_servico_id')->nullable()->constrained('produtos_servicos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos_clientes', function (Blueprint $table) {
            $table->dropForeign(['produto_servico_id']);
            $table->dropColumn('produto_servico_id');
        });
    }
};
