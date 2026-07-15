<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            $table->string('chave')->unique();
            $table->text('valor')->nullable();
            $table->string('tipo')->default('string'); // string, boolean, integer, json
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuracoes');
    }
};
