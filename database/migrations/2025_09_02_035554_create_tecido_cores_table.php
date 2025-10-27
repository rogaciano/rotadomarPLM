<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tecido_cores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tecido_id')->constrained('tecidos')->onDelete('cascade');
            $table->string('nome');
            $table->string('codigo')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Índice para busca rápida por tecido
            $table->index('tecido_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecido_cores');
    }
};
