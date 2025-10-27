<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdutoCombinacao extends Model
{
    use HasFactory;

    protected $table = 'produto_combinacao';

    protected $fillable = [
        'produto_id',
        'descricao',
        'quantidade_pretendida',
        'observacoes'
    ];

    protected $casts = [
        'quantidade_pretendida' => 'integer'
    ];

    /**
     * Get the product that owns this combination
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Get the components for this combination
     */
    public function componentes(): HasMany
    {
        return $this->hasMany(ProdutoCombinacaoComponente::class);
    }
}
