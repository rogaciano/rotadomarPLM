<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tecido extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'descricao',
        'referencia',
        'data_cadastro',
        'ativo',
        'ultima_consulta_estoque',
        'quantidade_estoque'
    ];

    protected $casts = [
        'data_cadastro' => 'date',
        'ultima_consulta_estoque' => 'datetime',
        'quantidade_estoque' => 'decimal:2',
        'ativo' => 'boolean'
    ];

    // Relacionamento com produtos
    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'produto_tecido')
                    ->withPivot('consumo')
                    ->withTimestamps();
    }
    
    // Relacionamento com produtos que calculam necessidade (status.calc_necessidade = 1)
    public function produtosComNecessidade()
    {
        return $this->belongsToMany(Produto::class, 'produto_tecido')
                    ->withPivot('consumo')
                    ->withTimestamps()
                    ->whereHas('status', function($query) {
                        $query->where('calc_necessidade', 1);
                    });
    }
    
    /**
     * Calcula a necessidade total do tecido com base no consumo planejado de todos os produtos
     * Considera apenas produtos cujo status tenha calc_necessidade = 1
     * 
     * @return float
     */
    public function getNecessidadeTotalAttribute()
    {
        $total = 0;
        
        // Usar produtosComNecessidade para filtrar apenas produtos com calc_necessidade = 1
        foreach ($this->produtosComNecessidade as $produto) {
            $total += $produto->quantidade * $produto->pivot->consumo;
        }
        
        return $total;
    }

    /**
     * Relacionamento com estoques por cor
     */
    public function estoquesCores()
    {
        return $this->hasMany(TecidoCorEstoque::class);
    }

    /**
     * Retorna a soma de todas as quantidades de estoque por cor
     * 
     * @return float
     */
    public function getTotalEstoquePorCoresAttribute()
    {
        return $this->estoquesCores()->sum('quantidade');
    }
}
