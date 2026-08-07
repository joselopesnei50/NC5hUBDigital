<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('material_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('autor_type', ['admin', 'cliente']);
            $table->text('mensagem');
            $table->string('anexo_path')->nullable();
            $table->timestamps();

            $table->index(['material_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_replies');
    }
};
