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
            // Adicionar referência ao produto_localizacao para rastreamento
            if (!Schema::hasColumn('produto_alocacao_mensal', 'produto_localizacao_id')) {
                $table->unsignedBigInteger('produto_localizacao_id')->nullable()->after('produto_id');
                $table->foreign('produto_localizacao_id')
                    ->references('id')
                    ->on('produto_localizacao')
                    ->onDelete('cascade');
            }
            
            // Adicionar ordem_producao para rastreamento
            if (!Schema::hasColumn('produto_alocacao_mensal', 'ordem_producao')) {
                $table->string('ordem_producao', 30)->nullable()->after('tipo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto_alocacao_mensal', function (Blueprint $table) {
            if (Schema::hasColumn('produto_alocacao_mensal', 'produto_localizacao_id')) {
                $table->dropForeign(['produto_localizacao_id']);
                $table->dropColumn('produto_localizacao_id');
            }
            
            if (Schema::hasColumn('produto_alocacao_mensal', 'ordem_producao')) {
                $table->dropColumn('ordem_producao');
            }
        });
    }
};
