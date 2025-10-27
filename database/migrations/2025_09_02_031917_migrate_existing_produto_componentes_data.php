<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obter todos os produtos que têm componentes
        $produtos = DB::table('produtos')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('produto_componentes')
                      ->whereRaw('produto_componentes.produto_id = produtos.id');
            })
            ->get(['id']);

        foreach ($produtos as $produto) {
            // Agrupar componentes por descrição
            $componentesPorDescricao = DB::table('produto_componentes')
                ->where('produto_id', $produto->id)
                ->select('descricao', 'quantidade_pretendida')
                ->distinct()
                ->get();

            // Para cada descrição, criar uma combinação
            foreach ($componentesPorDescricao as $grupo) {
                // Se a descrição for nula, usar um valor padrão
                $descricao = $grupo->descricao ?? 'Combinação Padrão';
                $quantidadePretendida = $grupo->quantidade_pretendida ?? 0;

                // Criar a combinação
                $combinacaoId = DB::table('produto_combinacao')->insertGetId([
                    'produto_id' => $produto->id,
                    'descricao' => $descricao,
                    'quantidade_pretendida' => $quantidadePretendida,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Atualizar os componentes para apontar para a nova combinação
                DB::table('produto_componentes')
                    ->where('produto_id', $produto->id)
                    ->where(function ($query) use ($descricao) {
                        $query->where('descricao', $descricao)
                              ->orWhereNull('descricao');
                    })
                    ->update([
                        'produto_combinacao_id' => $combinacaoId,
                        'updated_at' => now()
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Obter todas as combinações
        $combinacoes = DB::table('produto_combinacao')->get(['id', 'produto_id', 'descricao', 'quantidade_pretendida']);

        foreach ($combinacoes as $combinacao) {
            // Atualizar os componentes para restaurar os valores originais
            DB::table('produto_componentes')
                ->where('produto_combinacao_id', $combinacao->id)
                ->update([
                    'descricao' => $combinacao->descricao,
                    'quantidade_pretendida' => $combinacao->quantidade_pretendida,
                    'produto_combinacao_id' => null,
                    'updated_at' => now()
                ]);
        }

        // Limpar a tabela de combinações
        DB::table('produto_combinacao')->delete();
    }
};
