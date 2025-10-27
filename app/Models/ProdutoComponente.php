<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Classe de compatibilidade para registros antigos no log de atividades
 * Esta classe foi substituída por ProdutoCombinacaoComponente
 */
class ProdutoComponente extends Model
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
}
