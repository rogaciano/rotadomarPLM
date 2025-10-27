<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'status';

    protected $fillable = [
        'descricao',
        'ativo',
        'calc_necessidade',
        'observacoes'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'calc_necessidade' => 'boolean',
    ];

    // Relacionamento com produtos
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
