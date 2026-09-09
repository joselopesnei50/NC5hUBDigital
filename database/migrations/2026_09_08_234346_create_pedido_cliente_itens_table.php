<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedido_cliente_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_cliente_id')->constrained('pedidos_clientes')->onDelete('cascade');
            $table->foreignId('produto_servico_id')->nullable()->constrained('produtos_servicos')->nullOnDelete();
            $table->string('nome_item');
            $table->decimal('quantidade', 10, 2)->default(1);
            $table->decimal('valor_unitario', 12, 2)->default(0);
            $table->decimal('valor_total', 12, 2)->default(0);
            $table->timestamps();
        });

        // Data Backfill
        $pedidos = DB::table('pedidos_clientes')->get();
        foreach ($pedidos as $pedido) {
            DB::table('pedido_cliente_itens')->insert([
                'pedido_cliente_id' => $pedido->id,
                'produto_servico_id' => $pedido->produto_servico_id,
                'nome_item' => $pedido->titulo,
                'quantidade' => 1,
                'valor_unitario' => $pedido->valor,
                'valor_total' => $pedido->valor,
                'created_at' => $pedido->created_at,
                'updated_at' => $pedido->updated_at,
            ]);
        }

        // Drop legacy column
        Schema::table('pedidos_clientes', function (Blueprint $table) {
            $table->dropForeign(['produto_servico_id']);
            $table->dropColumn('produto_servico_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos_clientes', function (Blueprint $table) {
            $table->foreignId('produto_servico_id')->nullable()->constrained('produtos_servicos')->nullOnDelete();
        });

        // Reverse backfill (take the first item of each order as the main one)
        $itens = DB::table('pedido_cliente_itens')->groupBy('pedido_cliente_id')->get();
        foreach ($itens as $item) {
            DB::table('pedidos_clientes')->where('id', $item->pedido_cliente_id)->update([
                'produto_servico_id' => $item->produto_servico_id
            ]);
        }

        Schema::dropIfExists('pedido_cliente_itens');
    }
};
