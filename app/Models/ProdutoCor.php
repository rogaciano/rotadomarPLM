<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoCor extends Model
{
    use HasFactory;

    protected $table = 'produto_cor';

    protected $fillable = [
        'produto_id',
        'cor',
        'codigo_cor',
        'cor_rgb',
        'quantidade'
    ];

    protected $casts = [
        'quantidade' => 'integer'
    ];

    /**
     * Relacionamento com o produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
