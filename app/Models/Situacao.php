<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Situacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'situacoes';

    protected $fillable = [
        'descricao',
        'ativo',
        'prazo',
        'observacoes'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'prazo' => 'integer',
        'observacoes' => 'string'
    ];

    // Relacionamento com movimentacoes
    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class);
    }
}
