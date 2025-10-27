<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoRedistribuicaoHistorico extends Model
{
    use HasFactory;

    protected $table = 'produto_redistribuicao_historico';

    // Não usar updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'produto_id',
        'localizacao_origem_id',
        'data_prevista_origem',
        'mes_origem',
        'ano_origem',
        'localizacao_destino_id',
        'data_prevista_destino',
        'mes_destino',
        'ano_destino',
        'quantidade',
        'motivo',
        'tipo_redistribuicao',
        'usuario_id',
        'observacoes'
    ];

    protected $casts = [
        'data_prevista_origem' => 'date',
        'data_prevista_destino' => 'date',
        'mes_origem' => 'integer',
        'ano_origem' => 'integer',
        'mes_destino' => 'integer',
        'ano_destino' => 'integer',
        'quantidade' => 'integer'
    ];

    // Relacionamentos
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function localizacaoOrigem()
    {
        return $this->belongsTo(Localizacao::class, 'localizacao_origem_id');
    }

    public function localizacaoDestino()
    {
        return $this->belongsTo(Localizacao::class, 'localizacao_destino_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('produto_id', $produtoId);
    }

    public function scopePorLocalizacao($query, $localizacaoId)
    {
        return $query->where('localizacao_origem_id', $localizacaoId)
                    ->orWhere('localizacao_destino_id', $localizacaoId);
    }

    public function scopePorPeriodo($query, $mes, $ano)
    {
        return $query->where(function($q) use ($mes, $ano) {
            $q->where(function($subQ) use ($mes, $ano) {
                $subQ->where('mes_origem', $mes)
                     ->where('ano_origem', $ano);
            })
            ->orWhere(function($subQ) use ($mes, $ano) {
                $subQ->where('mes_destino', $mes)
                     ->where('ano_destino', $ano);
            });
        });
    }
}
