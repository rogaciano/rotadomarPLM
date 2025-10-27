<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProdutoLocalizacao extends Pivot
{
    use HasFactory;

    protected $table = 'produto_localizacao';
    
    // Habilitar auto-incremento para ter acesso ao ID do pivot
    public $incrementing = true;

    protected $fillable = [
        'produto_id',
        'localizacao_id',
        'quantidade',
        'data_prevista_faccao',
        'ordem_producao',
        'observacao'
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'data_prevista_faccao' => 'date'
    ];

    /**
     * Relacionamento com o produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Relacionamento com a localização
     */
    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class);
    }
}
