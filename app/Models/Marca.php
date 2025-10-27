<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Produto;

class Marca extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome_marca',
        'logo_path',
        'cor_fundo',
        'cor_fonte',
        'ativo',
        'suporte_marca'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamento com produtos
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

    /**
     * Get total number of products for this brand
     */
    public function getTotalProdutosAttribute()
    {
        return $this->produtos()->count();
    }

    /**
     * Get product count by estilista
     */
    public function getProdutosPorEstilista()
    {
        return $this->produtos()
            ->selectRaw('estilista_id, count(*) as total')
            ->with('estilista')
            ->groupBy('estilista_id')
            ->orderBy('total', 'desc')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->estilista ? $item->estilista->nome_estilista : 'Sem Estilista' => $item->total];
            });
    }

    /**
     * Get product count by localizacao (from last movimentacao)
     */
    public function getProdutosPorLocalizacao()
    {
        // Use a subquery approach to avoid ONLY_FULL_GROUP_BY issues
        $latestMovimentacoes = \DB::table('movimentacoes as m1')
            ->select('m1.produto_id', 'm1.localizacao_id')
            ->whereIn('m1.id', function($query) {
                $query->select(\DB::raw('MAX(m2.id)'))
                    ->from('movimentacoes as m2')
                    ->whereRaw('m2.produto_id = m1.produto_id')
                    ->groupBy('m2.produto_id');
            });

        return \DB::table('produtos')
            ->joinSub($latestMovimentacoes, 'latest_mov', function($join) {
                $join->on('produtos.id', '=', 'latest_mov.produto_id');
            })
            ->join('localizacoes', 'latest_mov.localizacao_id', '=', 'localizacoes.id')
            ->where('produtos.marca_id', $this->id)
            ->whereNull('produtos.deleted_at')
            ->select('localizacoes.nome_localizacao', \DB::raw('count(*) as total'))
            ->groupBy('localizacoes.nome_localizacao')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'nome_localizacao')
            ->toArray();
    }

    /**
     * Get product count by grupo
     */
    public function getProdutosPorGrupo()
    {
        return $this->produtos()
            ->selectRaw('grupo_id, count(*) as total')
            ->with('grupoProduto')
            ->groupBy('grupo_id')
            ->orderBy('total', 'desc')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->grupoProduto ? $item->grupoProduto->descricao : 'Sem Grupo' => $item->total];
            });
    }

    /**
     * Get product count by status
     */
    public function getProdutosPorStatus()
    {
        return $this->produtos()
            ->selectRaw('status_id, count(*) as total')
            ->with('status')
            ->groupBy('status_id')
            ->orderBy('total', 'desc')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->status ? $item->status->descricao : 'Sem Status' => $item->total];
            });
    }
}
