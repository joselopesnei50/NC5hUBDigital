<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prestadores', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('tipo_pessoa', ['PF', 'PJ'])->default('PF');
            $table->string('cpf_cnpj', 20)->nullable();
            $table->string('rg_ie', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('telefone', 30)->nullable();
            $table->string('endereco')->nullable();
            $table->string('cidade', 120)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('categoria', 80)->nullable();
            $table->string('banco', 80)->nullable();
            $table->string('agencia', 20)->nullable();
            $table->string('conta', 30)->nullable();
            $table->string('chave_pix', 120)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('nome');
            $table->index('ativo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prestadores');
    }
};
