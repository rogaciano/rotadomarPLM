<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TecidoCor extends Model
{
    use HasFactory;

    protected $table = 'tecido_cores';

    protected $fillable = [
        'tecido_id',
        'nome',
        'codigo',
        'observacoes'
    ];

    /**
     * Relacionamento com o tecido
     */
    public function tecido()
    {
        return $this->belongsTo(Tecido::class);
    }

    /**
     * Relacionamento com o estoque da cor
     */
    public function estoque()
    {
        return $this->hasOne(TecidoCorEstoque::class, 'codigo_cor', 'codigo')
            ->where('tecido_id', $this->tecido_id);
    }
}
