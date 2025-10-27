<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'descricao',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'observacoes' => 'string'
    ];

    // Relacionamento com movimentacoes
    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class);
    }
}
