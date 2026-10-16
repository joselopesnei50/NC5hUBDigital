<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('template')->default('corporativo'); // corporativo, servicos_locais, catalogo_vitrine, captura_alta_conversao
            $table->boolean('is_published')->default(false);

            // SEO & Identidade
            $table->string('titulo_pagina');
            $table->string('meta_descricao', 500)->nullable();
            $table->string('cor_primaria', 20)->default('#6366f1');
            $table->string('cor_secundaria', 20)->default('#0f172a');
            $table->string('logo_path')->nullable();
            $table->string('hero_image_path')->nullable();

            // Seção Hero (Topo)
            $table->string('hero_badge')->nullable();
            $table->string('hero_titulo');
            $table->text('hero_subtitulo')->nullable();
            $table->string('hero_cta_texto')->default('Falar no WhatsApp');

            // Seção Sobre Nós
            $table->string('sobre_titulo')->nullable();
            $table->text('sobre_texto')->nullable();

            // Seções estruturadas (JSON)
            $table->json('servicos_json')->nullable();
            $table->json('diferenciais_json')->nullable();
            $table->json('depoimentos_json')->nullable();
            $table->json('faq_json')->nullable();

            // Contato & Redes Sociais
            $table->string('whatsapp_numero', 40)->nullable();
            $table->string('telefone', 40)->nullable();
            $table->string('email_contato')->nullable();
            $table->string('endereco_texto')->nullable();
            $table->string('horario_atendimento')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->text('google_maps_embed')->nullable();

            // Contadores de performance
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('whatsapp_clicks_count')->default(0);
            $table->unsignedBigInteger('leads_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
