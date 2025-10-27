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
        Schema::create('produto_cor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->string('cor', 100); // Nome da cor (vem do tecido)
            $table->string('codigo_cor', 50)->nullable(); // Código da cor (vem do tecido)
            $table->string('cor_rgb', 7)->nullable(); // Formato #RRGGBB
            $table->integer('quantidade'); // Quantidade desta cor específica
            $table->timestamps();
            
            // Ensure unique combination of produto_id, cor and codigo_cor
            $table->unique(['produto_id', 'cor', 'codigo_cor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_cor');
    }
};
