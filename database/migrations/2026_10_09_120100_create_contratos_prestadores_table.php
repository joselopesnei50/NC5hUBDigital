<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contratos_prestadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestador_id')->constrained('prestadores')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('objeto');
            $table->decimal('valor', 12, 2)->default(0);
            $table->enum('forma_pagamento', ['mensal', 'por_entrega', 'unico', 'hora', 'outro'])->default('mensal');
            $table->string('periodicidade', 60)->nullable();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('prazo_entrega', 120)->nullable();
            $table->boolean('clausula_confidencialidade')->default(true);
            $table->boolean('clausula_exclusividade')->default(false);
            $table->boolean('clausula_propriedade_intelectual')->default(true);
            $table->boolean('clausula_nao_vinculo_empregaticio')->default(true);
            $table->longText('clausulas_extras')->nullable();
            $table->longText('conteudo')->nullable();
            $table->enum('status', ['rascunho', 'enviado', 'assinado', 'cancelado', 'encerrado'])->default('rascunho');
            $table->string('status_assinatura', 30)->default('pendente');
            $table->longText('assinatura_url')->nullable();
            $table->timestamp('assinado_em')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('prestador_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contratos_prestadores');
    }
};
