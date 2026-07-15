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
    public function up()
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('servico_id')->constrained()->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('status')->default('ativo');
            $table->string('pdf_path')->nullable();
            $table->string('assinatura_url')->nullable();
            $table->string('status_assinatura')->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contratos');
    }
};
