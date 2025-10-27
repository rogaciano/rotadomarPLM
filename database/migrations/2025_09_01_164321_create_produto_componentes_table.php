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
        Schema::create('produto_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained()->onDelete('cascade');
            $table->foreignId('tecido_id')->constrained()->onDelete('cascade');
            $table->string('cor')->nullable();
            $table->string('codigo_cor')->nullable();
            $table->decimal('consumo', 10, 2)->comment('Consumo de tecido em gramas/metros por unidade');
            $table->decimal('quantidade', 10, 2)->default(0)->comment('Quantidade de unidades a serem produzidas');
            $table->decimal('porcentagem', 5, 2)->nullable()->comment('Porcentagem do total de tecido');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Índices para melhorar a performance
            $table->index(['produto_id', 'tecido_id']);
            $table->index(['cor', 'codigo_cor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_componentes');
    }
};
