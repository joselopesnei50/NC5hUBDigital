<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cliente_id')
                ->nullable()
                ->after('role')
                ->constrained('clientes')
                ->nullOnDelete();
        });

        DB::table('clientes')->orderBy('id')->chunkById(500, function ($clientes) {
            foreach ($clientes as $cliente) {
                DB::table('users')
                    ->where('id', $cliente->user_id)
                    ->update(['cliente_id' => $cliente->id]);
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');
        });
    }
};
