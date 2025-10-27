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
        Schema::create('produto_tecido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->foreignId('tecido_id')->constrained('tecidos')->onDelete('cascade');
            $table->decimal('consumo', 8, 3)->nullable(); // Consumption amount
            $table->timestamps();
            
            // Ensure unique combination of produto_id and tecido_id
            $table->unique(['produto_id', 'tecido_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_tecido');
    }
};
