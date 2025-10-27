<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TecidoCorEstoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'tecido_id',
        'cor',
        'codigo_cor',
        'quantidade',
        'quantidade_pretendida',
        'data_atualizacao',
        'observacoes'
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'quantidade_pretendida' => 'decimal:2',
        'data_atualizacao' => 'date'
    ];

    /**
     * Relacionamento com o tecido
     */
    public function tecido()
    {
        return $this->belongsTo(Tecido::class);
    }

    /**
     * Calcula a necessidade total desta cor baseada nos produtos que usam o tecido
     * Necessidade = (Quantidade da cor no produto × Consumo do tecido) + (Necessidade das combinações)
     * 
     * @return float
     */
    public function getNecessidadeAttribute()
    {
        $necessidade = 0;
        
        // Parte 1: Necessidade das variações de cores
        // Buscar todos os produtos que usam este tecido
        $produtos = $this->tecido->produtos;
        
        foreach ($produtos as $produto) {
            // Buscar se o produto tem esta cor específica
            $produtoCor = $produto->cores()
                ->where('cor', $this->cor)
                ->first();
            
            if ($produtoCor) {
                // Necessidade = Quantidade da cor no produto × Consumo do tecido
                $necessidade += $produtoCor->quantidade * $produto->pivot->consumo;
            }
            
            // Parte 2: Necessidade das combinações de cores
            // Buscar todas as combinações do produto
            $combinacoes = $produto->combinacoes;
            
            foreach ($combinacoes as $combinacao) {
                // Buscar componentes da combinação que usam este tecido e esta cor
                $componentes = $combinacao->componentes()
                    ->where('tecido_id', $this->tecido_id)
                    ->where('cor', $this->cor)
                    ->get();
                
                foreach ($componentes as $componente) {
                    // Necessidade da combinação = Quantidade pretendida da combinação × Consumo do componente
                    $necessidade += $combinacao->quantidade_pretendida * $componente->consumo;
                }
            }
        }
        
        return $necessidade;
    }

    /**
     * Calcula o saldo (estoque - necessidade)
     * 
     * @return float
     */
    public function getSaldoAttribute()
    {
        return $this->quantidade - $this->necessidade;
    }
    
    /**
     * Calcula quantos produtos podem ser fabricados com o estoque disponível desta cor
     * 
     * @return array
     */
    public function getProdutosPossiveisAttribute()
    {
        $resultado = [];
        $estoque = $this->quantidade;
        
        if ($estoque <= 0) {
            return $resultado;
        }
        
        // Buscar todos os produtos que usam este tecido
        $produtos = $this->tecido->produtos;
        
        foreach ($produtos as $produto) {
            // Buscar se o produto tem esta cor específica
            $produtoCor = $produto->cores()
                ->where('cor', $this->cor)
                ->first();
            
            if ($produtoCor && $produto->pivot->consumo > 0) {
                // Quantidade possível = Estoque disponível / Consumo do tecido
                $quantidadePossivel = floor($estoque / $produto->pivot->consumo);
                
                if ($quantidadePossivel > 0) {
                    $resultado[] = [
                        'produto_id' => $produto->id,
                        'referencia' => $produto->referencia,
                        'descricao' => $produto->descricao,
                        'consumo' => $produto->pivot->consumo,
                        'quantidade_possivel' => $quantidadePossivel
                    ];
                }
            }
        }
        
        return $resultado;
    }
    
    /**
     * Retorna os produtos que usam esta cor de tecido com necessidade maior que zero
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getProdutosComNecessidadeAttribute()
    {
        $produtosComNecessidade = collect();
        
        if ($this->necessidade <= 0) {
            return $produtosComNecessidade;
        }
        
        // Parte 1: Produtos com variações de cores
        $produtos = $this->tecido->produtos;
        
        foreach ($produtos as $produto) {
            $produtoCor = $produto->cores()
                ->where('cor', $this->cor)
                ->first();
            
            if ($produtoCor && $produtoCor->quantidade > 0) {
                $necessidadeProduto = $produtoCor->quantidade * $produto->pivot->consumo;
                
                if ($necessidadeProduto > 0) {
                    $produtosComNecessidade->push([
                        'id' => $produto->id,
                        'referencia' => $produto->referencia,
                        'descricao' => $produto->descricao,
                        'necessidade' => $necessidadeProduto,
                        'tipo' => 'variação'
                    ]);
                }
            }
            
            // Parte 2: Produtos com combinações de cores
            $combinacoes = $produto->combinacoes;
            
            foreach ($combinacoes as $combinacao) {
                $componentes = $combinacao->componentes()
                    ->where('tecido_id', $this->tecido_id)
                    ->where('cor', $this->cor)
                    ->get();
                
                foreach ($componentes as $componente) {
                    $necessidadeCombinacao = $combinacao->quantidade_pretendida * $componente->consumo;
                    
                    if ($necessidadeCombinacao > 0) {
                        $produtosComNecessidade->push([
                            'id' => $produto->id,
                            'referencia' => $produto->referencia,
                            'descricao' => $produto->descricao . ' (' . $combinacao->descricao . ')',
                            'necessidade' => $necessidadeCombinacao,
                            'tipo' => 'combinação'
                        ]);
                    }
                }
            }
        }
        
        return $produtosComNecessidade->unique('id');
    }
}
