<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('termos_imagem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('titulo');
            $table->string('projeto_referencia')->nullable();
            $table->date('data_captacao')->nullable();
            $table->string('local_captacao')->nullable();

            // Dados do cedente
            $table->string('cedente_nome');
            $table->string('cedente_cpf', 30);
            $table->string('cedente_rg', 40)->nullable();
            $table->string('cedente_email')->nullable();
            $table->string('cedente_telefone', 40)->nullable();
            $table->string('cedente_endereco')->nullable();

            // Menor de idade
            $table->boolean('cedente_menor')->default(false);
            $table->string('responsavel_nome')->nullable();
            $table->string('responsavel_cpf', 30)->nullable();
            $table->string('responsavel_parentesco', 60)->nullable();

            // Conteúdo e assinatura
            $table->longText('conteudo');
            $table->string('status', 30)->default('rascunho');
            $table->string('signature_token', 64)->nullable()->unique();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('assinado_em')->nullable();
            $table->longText('assinatura_dados')->nullable();
            $table->string('conteudo_hash', 64)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('cliente_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('termos_imagem');
    }
};
