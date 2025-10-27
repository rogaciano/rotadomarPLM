<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocalizacaoCapacidadeMensal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'localizacao_capacidade_mensal';

    protected $fillable = [
        'localizacao_id',
        'mes',
        'ano',
        'capacidade',
        'observacoes'
    ];

    protected $casts = [
        'mes' => 'integer',
        'ano' => 'integer',
        'capacidade' => 'integer'
    ];

    // Relacionamento com Localizacao
    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class);
    }

    // Método auxiliar para formatar mês/ano em português
    public function getMesAnoFormatadoAttribute()
    {
        $meses = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];
        
        return $meses[$this->mes] . '/' . $this->ano;
    }

    // Método para calcular a quantidade total de produtos previstos para este período
    public function getProdutosPrevistos()
    {
        // Somar as quantidades das alocações mensais (garantindo que pegue apenas valores válidos)
        $total = \App\Models\ProdutoAlocacaoMensal::where('localizacao_id', $this->localizacao_id)
            ->where('mes', $this->mes)
            ->where('ano', $this->ano)
            ->whereNotNull('quantidade')
            ->sum('quantidade');
        
        // Garantir que retorna um inteiro válido
        return (int) ($total ?? 0);
    }

    // Método para calcular o saldo (capacidade - produtos previstos)
    public function getSaldo()
    {
        return $this->capacidade - $this->getProdutosPrevistos();
    }

    // Método para verificar se está acima da capacidade
    public function isAcimaDaCapacidade()
    {
        return $this->getProdutosPrevistos() > $this->capacidade;
    }

    // Método para calcular percentual de ocupação
    public function getPercentualOcupacao()
    {
        if ($this->capacidade == 0) {
            return 0;
        }
        
        return round(($this->getProdutosPrevistos() / $this->capacidade) * 100, 1);
    }
}
