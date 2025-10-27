<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdutoCombinacaoComponente extends Model
{
    use HasFactory;

    protected $table = 'produto_combinacao_componente';

    protected $fillable = [
        'produto_combinacao_id',
        'tecido_id',
        'cor',
        'codigo_cor',
        'consumo'
    ];

    protected $casts = [
        'consumo' => 'decimal:3'
    ];

    /**
     * Get the combination that owns this component
     */
    public function combinacao(): BelongsTo
    {
        return $this->belongsTo(ProdutoCombinacao::class, 'produto_combinacao_id');
    }

    /**
     * Get the fabric associated with this component
     */
    public function tecido(): BelongsTo
    {
        return $this->belongsTo(Tecido::class);
    }
}
