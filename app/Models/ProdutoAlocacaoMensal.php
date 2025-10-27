<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoAlocacaoMensal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto_alocacao_mensal';

    protected $fillable = [
        'produto_id',
        'produto_localizacao_id',
        'localizacao_id',
        'mes',
        'ano',
        'quantidade',
        'tipo',
        'ordem_producao',
        'observacoes',
        'usuario_id'
    ];

    protected $casts = [
        'mes' => 'integer',
        'ano' => 'integer',
        'quantidade' => 'integer'
    ];

    // Relacionamentos
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function produtoLocalizacao()
    {
        return $this->belongsTo(ProdutoLocalizacao::class);
    }

    // Scopes
    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('produto_id', $produtoId);
    }

    public function scopePorPeriodo($query, $mes, $ano)
    {
        return $query->where('mes', $mes)->where('ano', $ano);
    }

    public function scopePorLocalizacao($query, $localizacaoId)
    {
        return $query->where('localizacao_id', $localizacaoId);
    }

    // Método auxiliar para formatar mês/ano
    public function getMesAnoFormatadoAttribute()
    {
        $meses = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];
        
        return $meses[$this->mes] . '/' . $this->ano;
    }

    // Validação: Soma das alocações não pode ultrapassar quantidade do produto
    public static function validarQuantidadeTotal($produtoId, $quantidadeNova, $alocacaoIdExcluir = null)
    {
        $produto = Produto::find($produtoId);
        if (!$produto) {
            return false;
        }

        $query = self::where('produto_id', $produtoId);
        
        if ($alocacaoIdExcluir) {
            $query->where('id', '!=', $alocacaoIdExcluir);
        }

        $totalAlocado = $query->sum('quantidade');
        $novoTotal = $totalAlocado + $quantidadeNova;

        return $novoTotal <= $produto->quantidade;
    }

    // Método para obter quantidade disponível do produto
    public static function getQuantidadeDisponivel($produtoId)
    {
        $produto = Produto::find($produtoId);
        if (!$produto) {
            return 0;
        }

        $totalAlocado = self::where('produto_id', $produtoId)->sum('quantidade');
        return $produto->quantidade - $totalAlocado;
    }
}
