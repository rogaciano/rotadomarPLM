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
        Schema::create('produto_combinacao_componente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_combinacao_id')->constrained('produto_combinacao')->onDelete('cascade');
            $table->foreignId('tecido_id')->constrained('tecidos');
            $table->string('cor');
            $table->string('codigo_cor')->nullable();
            $table->decimal('consumo', 10, 3)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_combinacao_componente');
    }
};
