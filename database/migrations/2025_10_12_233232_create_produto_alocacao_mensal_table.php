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
        Schema::create('produto_alocacao_mensal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('localizacao_id');
            $table->tinyInteger('mes')->comment('1-12');
            $table->smallInteger('ano')->comment('Ex: 2025');
            $table->integer('quantidade');
            $table->string('tipo', 50)->default('original')->comment('original, redistribuido');
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('produto_id');
            $table->index(['mes', 'ano']);
            $table->index(['localizacao_id', 'mes', 'ano']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_alocacao_mensal');
    }
};
