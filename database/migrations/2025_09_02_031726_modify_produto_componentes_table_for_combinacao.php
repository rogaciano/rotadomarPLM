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
        Schema::table('produto_componentes', function (Blueprint $table) {
            // Primeiro, remover os campos que serão movidos para a tabela produto_combinacao
            $table->dropColumn(['descricao', 'quantidade_pretendida']);
            
            // Adicionar a nova chave estrangeira para produto_combinacao
            $table->foreignId('produto_combinacao_id')->after('produto_id')->nullable();
            
            // Adicionar índice para a nova chave estrangeira
            $table->index('produto_combinacao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto_componentes', function (Blueprint $table) {
            // Remover a chave estrangeira e o índice
            $table->dropIndex(['produto_combinacao_id']);
            $table->dropColumn('produto_combinacao_id');
            
            // Restaurar os campos que foram movidos
            $table->string('descricao', 255)->nullable()->after('produto_id');
            $table->decimal('quantidade_pretendida', 10, 2)->nullable()->default(0)->after('quantidade');
        });
    }
};
