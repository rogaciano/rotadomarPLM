<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoAnexo extends Model
{
    use HasFactory;

    protected $table = 'produto_anexos';

    protected $fillable = [
        'produto_id',
        'descricao',
        'arquivo_path',
        'tipo_arquivo'
    ];

    /**
     * Relacionamento com o produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
