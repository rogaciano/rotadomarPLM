<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoProduto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grupos';

    protected $fillable = [
        'descricao',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamento com produtos
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'grupo_id');
    }
}
