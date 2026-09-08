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
            $table->string('token_publico', 64)->nullable()->unique();
            $table->string('ip_aprovacao', 45)->nullable();
            $table->string('nome_aprovacao', 255)->nullable();
            $table->timestamp('data_aprovacao')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos_clientes', function (Blueprint $table) {
            $table->dropColumn(['token_publico', 'ip_aprovacao', 'nome_aprovacao', 'data_aprovacao']);
        });
    }
};
