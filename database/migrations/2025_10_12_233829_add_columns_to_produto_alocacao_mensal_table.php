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
        Schema::table('produto_alocacao_mensal', function (Blueprint $table) {
            // Adicionar colunas faltantes
            if (!Schema::hasColumn('produto_alocacao_mensal', 'tipo')) {
                $table->string('tipo', 50)->default('original')->comment('original, redistribuido')->after('quantidade');
            }
            if (!Schema::hasColumn('produto_alocacao_mensal', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('tipo');
            }
            if (!Schema::hasColumn('produto_alocacao_mensal', 'usuario_id')) {
                $table->unsignedBigInteger('usuario_id')->nullable()->after('observacoes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto_alocacao_mensal', function (Blueprint $table) {
            if (Schema::hasColumn('produto_alocacao_mensal', 'tipo')) {
                $table->dropColumn('tipo');
            }
            if (Schema::hasColumn('produto_alocacao_mensal', 'observacoes')) {
                $table->dropColumn('observacoes');
            }
            if (Schema::hasColumn('produto_alocacao_mensal', 'usuario_id')) {
                $table->dropColumn('usuario_id');
            }
        });
    }
};
