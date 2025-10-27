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
        Schema::create('produto_combinacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained()->onDelete('cascade');
            $table->string('descricao')->nullable();
            $table->decimal('quantidade_pretendida', 10, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Índice para melhorar a performance
            $table->index('produto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_combinacao');
    }
};
