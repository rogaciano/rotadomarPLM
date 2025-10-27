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
        Schema::table('produto_redistribuicao_historico', function (Blueprint $table) {
            // Tornar colunas de data nullable (agora trabalhamos com mês/ano das alocações)
            $table->date('data_prevista_origem')->nullable()->change();
            $table->date('data_prevista_destino')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto_redistribuicao_historico', function (Blueprint $table) {
            // Reverter para NOT NULL
            $table->date('data_prevista_origem')->nullable(false)->change();
            $table->date('data_prevista_destino')->nullable(false)->change();
        });
    }
};
