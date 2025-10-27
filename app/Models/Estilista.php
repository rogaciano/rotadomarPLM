<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Produto;
use App\Models\Marca;

class Estilista extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'nome_estilista',
        'ativo',
        'marca_id',
        'suporte_marca',
        'foto',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Acessor para obter a URL completa da foto do estilista
     *
     * @return string
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        
        // Retorna uma imagem padrão caso não haja foto
        return asset('images/default-estilista.jpg');
    }

    /**
     * Obter a marca associada a este estilista.
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Relacionamento com os produtos do estilista
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

    /**
     * Retorna o total de produtos do estilista
     *
     * @return int
     */
    public function totalProdutos()
    {
        return $this->produtos()->count();
    }

    /**
     * Retorna a contagem de produtos agrupados por marca
     *
     * @return array
     */
    public function produtosPorMarca()
    {
        $result = [];
        $produtos = $this->produtos()->with('marca')->get();
        
        foreach ($produtos as $produto) {
            if ($produto->marca) {
                $marca = $produto->marca->nome_marca;
                if (!isset($result[$marca])) {
                    $result[$marca] = 0;
                }
                $result[$marca]++;
            }
        }
        
        return $result;
    }

    /**
     * Retorna a contagem de produtos agrupados por status
     *
     * @return array
     */
    public function produtosPorStatus()
    {
        $result = [];
        $produtos = $this->produtos()->with('status')->get();
        
        foreach ($produtos as $produto) {
            if ($produto->status) {
                $status = $produto->status->descricao;
                if (!isset($result[$status])) {
                    $result[$status] = 0;
                }
                $result[$status]++;
            }
        }
        
        return $result;
    }

    /**
     * Retorna a contagem de produtos agrupados por grupo
     * Retorna os 10 primeiros grupos e agrupa os demais em 'Outros'
     *
     * @return array
     */
    public function produtosPorGrupo()
    {
        $result = [];
        $produtos = $this->produtos()->with('grupoProduto')->get();
        
        // Contagem por grupo
        foreach ($produtos as $produto) {
            if ($produto->grupoProduto) {
                $grupo = $produto->grupoProduto->descricao;
                if (!isset($result[$grupo])) {
                    $result[$grupo] = 0;
                }
                $result[$grupo]++;
            }
        }
        
        // Ordena por quantidade (maior para menor)
        arsort($result);
        
        // Separa os 10 primeiros e soma os demais em 'Outros'
        $top10 = array_slice($result, 0, 10, true);
        $outros = array_slice($result, 10, null, true);
        
        if (count($outros) > 0) {
            $top10['Outros'] = array_sum($outros);
        }
        
        return $top10;
    }

    /**
     * Retorna a contagem de produtos agrupados por localização
     * Retorna as 10 primeiras localizações e agrupa as demais em 'Outros'
     *
     * @return array
     */
    public function produtosPorLocalizacao()
    {
        $result = [];
        $produtos = $this->produtos()->with('movimentacoes.localizacao')->get();
        
        // Contagem por localização
        foreach ($produtos as $produto) {
            // Pega a última movimentação (localização atual)
            $ultimaMovimentacao = $produto->movimentacoes->sortByDesc('id')->first();
            
            if ($ultimaMovimentacao && $ultimaMovimentacao->localizacao) {
                $localizacao = $ultimaMovimentacao->localizacao->nome_localizacao;
                if (!isset($result[$localizacao])) {
                    $result[$localizacao] = 0;
                }
                $result[$localizacao]++;
            }
        }
        
        // Ordena por quantidade (maior para menor)
        arsort($result);
        
        // Separa as 10 primeiras e soma as demais em 'Outros'
        $top10 = array_slice($result, 0, 10, true);
        $outros = array_slice($result, 10, null, true);
        
        if (count($outros) > 0) {
            $top10['Outros'] = array_sum($outros);
        }
        
        return $top10;
    }

    /**
     * Calcula o tempo médio desde a criação até a ativação dos produtos
     * Usa a data da primeira movimentação como data de ativação
     *
     * @return string|null
     */
    /**
     * Retorna os dados mensais de produtos do estilista
     * Últimos 12 meses
     *
     * @return array
     */
    public function produtosPorMes()
    {
        $data = [];
        $labels = [];
        $valores = [];
        
        // Inicializa os últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $dataAtual = now()->subMonths($i);
            $mesAno = $dataAtual->format('m/Y');
            $labels[] = $dataAtual->translatedFormat('M/Y');
            $data[$mesAno] = 0;
        }
        
        // Conta produtos por mês/ano
        $produtos = $this->produtos()
            ->select('id', 'created_at')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('m/Y');
            });
        
        // Preenche os valores reais
        foreach ($data as $mesAno => $valor) {
            if (isset($produtos[$mesAno])) {
                $data[$mesAno] = $produtos[$mesAno]->count();
            }
            $valores[] = $data[$mesAno];
        }
        
        return [
            'labels' => $labels,
            'data' => $valores,
            'total' => array_sum($valores)
        ];
    }

    /**
     * Calcula o tempo médio desde a criação até a ativação dos produtos
     * Usa a data da primeira movimentação como data de ativação
     *
     * @return string|null
     */
    public function tempoMedioAtivacao()
    {
        $produtos = $this->produtos()
            ->with(['movimentacoes' => function($query) {
                $query->orderBy('data_entrada', 'asc')
                      ->limit(1);
            }])
            ->get();

        if ($produtos->isEmpty()) {
            return null;
        }

        $totalDias = 0;
        $count = 0;

        foreach ($produtos as $produto) {
            // Pega a primeira movimentação (mais antiga) como data de ativação
            $primeiraMovimentacao = $produto->movimentacoes->sortBy('data_entrada')->first();
            
            if ($primeiraMovimentacao && $produto->data_cadastro) {
                $diferenca = $produto->data_cadastro->diffInDays($primeiraMovimentacao->data_entrada);
                $totalDias += $diferenca;
                $count++;
            }
        }

        if ($count === 0) {
            return null;
        }

        $mediaDias = $totalDias / $count;
        
        if ($mediaDias < 1) {
            return 'Menos de 1 dia';
        }
        
        return round($mediaDias) . ' dias';
    }
}
