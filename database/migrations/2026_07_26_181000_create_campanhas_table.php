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
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('assunto')->nullable();
            $table->string('audience')->default('leads_ia'); // 'leads_ia' ou 'importacao'
            
            $table->string('brevo_campaign_id')->nullable();
            
            $table->string('status')->default('rascunho'); // rascunho, enviando, concluido, falha
            
            $table->timestamp('sent_at')->nullable();
            
            $table->json('metrics')->nullable(); // Para cachear aberturas, clicks
            
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
        Schema::dropIfExists('campanhas');
    }
};
