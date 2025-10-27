<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove os campos localizacao_id e data_prevista_faccao da tabela produtos
     * Estes dados agora estão na tabela produto_localizacao
     */
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Remover a foreign key constraint primeiro (se existir)
            try {
                $table->dropForeign(['localizacao_id']);
            } catch (\Exception $e) {
                // Foreign key pode não existir, continuar normalmente
            }
            
            // Remover as colunas
            $table->dropColumn(['localizacao_id', 'data_prevista_faccao']);
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Restaura os campos localizacao_id e data_prevista_faccao na tabela produtos
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->unsignedBigInteger('localizacao_id')->nullable()->after('status_id');
            $table->date('data_prevista_faccao')->nullable()->after('data_prevista_producao');
            
            // Recriar a foreign key se necessário
            $table->foreign('localizacao_id')
                ->references('id')
                ->on('localizacoes')
                ->onDelete('set null');
        });
    }
};
