<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migra os dados de localizacao_id e data_prevista_faccao 
     * da tabela produtos para produto_localizacao
     */
    public function up(): void
    {
        // Migrar dados existentes de produtos para produto_localizacao
        $produtos = DB::table('produtos')
            ->whereNotNull('localizacao_id')
            ->whereNull('deleted_at')
            ->select('id', 'localizacao_id', 'data_prevista_faccao', 'quantidade')
            ->get();

        foreach ($produtos as $produto) {
            // Verificar se já existe um registro para evitar duplicatas
            $existe = DB::table('produto_localizacao')
                ->where('produto_id', $produto->id)
                ->where('localizacao_id', $produto->localizacao_id)
                ->exists();

            if (!$existe) {
                DB::table('produto_localizacao')->insert([
                    'produto_id' => $produto->id,
                    'localizacao_id' => $produto->localizacao_id,
                    'quantidade' => $produto->quantidade ?? 0,
                    'data_prevista_faccao' => $produto->data_prevista_faccao,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Remove os registros migrados da tabela produto_localizacao
     */
    public function down(): void
    {
        // Opcional: Remover os registros criados
        // Nota: Só deve ser executado se você tiver certeza que quer reverter
        // DB::table('produto_localizacao')->truncate();
    }
};
