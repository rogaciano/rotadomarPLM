<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\ProdutoObservacao;

class Produto extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'referencia',
        'descricao',
        'data_cadastro',
        'data_prevista_producao',
        'marca_id',
        'quantidade',
        'estilista_id',
        'grupo_id',
        'preco_atacado',
        'preco_varejo',
        'status_id',
        'localizacao_id',
        'anexo_ficha_producao',
        'anexo_catalogo_vendas',
        'produto_original_id',
        'numero_reprogramacao'
    ];

    protected $casts = [
        'data_cadastro' => 'date',
        'data_prevista_producao' => 'date',
        'preco_atacado' => 'decimal:2',
        'preco_varejo' => 'decimal:2',
        'quantidade' => 'integer',
        'produto_original_id' => 'integer',
        'numero_reprogramacao' => 'integer'
    ];

    // Relacionamentos
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    // Relacionamento singular para o primeiro tecido (compatibilidade)
    public function tecido()
    {
        return $this->belongsToMany(Tecido::class, 'produto_tecido')
                    ->withPivot('consumo')
                    ->withTimestamps()
                    ->limit(1);
    }

    // Relacionamento plural para todos os tecidos
    public function tecidos()
    {
        return $this->belongsToMany(Tecido::class, 'produto_tecido')
                    ->withPivot('consumo')
                    ->withTimestamps();
    }

    public function estilista()
    {
        return $this->belongsTo(Estilista::class);
    }

    public function grupoProduto()
    {
        return $this->belongsTo(GrupoProduto::class, 'grupo_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function localizacao()
    {
        return $this->belongsTo(\App\Models\Localizacao::class);
    }

    // Relacionamento plural para todas as localizações
    public function localizacoes()
    {
        return $this->belongsToMany(Localizacao::class, 'produto_localizacao')
                    ->withPivot('id', 'quantidade', 'data_prevista_faccao', 'ordem_producao', 'observacao')
                    ->withTimestamps()
                    ->using(ProdutoLocalizacao::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'produto_id');
    }
    
    /**
     * Relacionamento com os anexos do produto
     */
    public function anexos()
    {
        return $this->hasMany(ProdutoAnexo::class);
    }

    /**
     * Relacionamento com as observações do produto
     */
    public function observacoes()
    {
        return $this->hasMany(ProdutoObservacao::class, 'produto_id', 'id')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Relacionamento com as variações de cores do produto
     */
    public function cores()
    {
        return $this->hasMany(ProdutoCor::class);
    }

    /**
     * Relacionamento com o produto original (quando este é uma reprogramação)
     */
    public function produtoOriginal()
    {
        return $this->belongsTo(Produto::class, 'produto_original_id');
    }

    /**
     * Relacionamento com as reprogramações deste produto
     */
    public function reprogramacoes()
    {
        return $this->hasMany(Produto::class, 'produto_original_id')
                    ->orderBy('numero_reprogramacao', 'asc');
    }

    /**
     * Verifica se este produto é uma reprogramação
     */
    public function isReprogramacao()
    {
        return !is_null($this->produto_original_id);
    }

    /**
     * Verifica se este produto pode ser reprogramado
     */
    public function podeSerReprogramado()
    {
        return is_null($this->produto_original_id);
    }

    /**
     * Verifica se este produto é a última reprogramação de uma sequência
     */
    public function isUltimaReprogramacao()
    {
        if (!$this->produto_original_id) {
            return false;
        }

        // Buscar a maior reprogramação do produto original
        $ultimaReprogramacao = Produto::where('produto_original_id', $this->produto_original_id)
            ->max('numero_reprogramacao');

        return $this->numero_reprogramacao == $ultimaReprogramacao;
    }

    /**
     * Verifica se este produto pode ter movimentações
     * Produtos origem podem ter movimentação se não tiverem reprogramações
     * Produtos reprogramados só podem ter movimentação se forem a última reprogramação
     */
    public function podeMovimentar()
    {
        // Se não é reprogramação (produto original)
        if (!$this->isReprogramacao()) {
            // Verifica se não tem reprogramações
            return $this->reprogramacoes()->count() == 0;
        }

        // Se é reprogramação, verifica se é a última
        return $this->isUltimaReprogramacao();
    }

    /**
     * Retorna a quantidade total do produto baseada na soma das quantidades por cor
     */
    public function getQuantidadeTotalPorCoresAttribute()
    {
        return $this->cores()->sum('quantidade');
    }

    /**
     * Retorna o mês e ano da data prevista de produção
     */
    public function getDataPrevistaProducaoMesAnoAttribute()
    {
        if (!$this->data_prevista_producao) {
            return 'N/A';
        }
        
        return $this->data_prevista_producao->format('m/Y');
    }
    
    /**
     * Relacionamento com as combinações de cores do produto
     */
    public function combinacoes()
    {
        return $this->hasMany(ProdutoCombinacao::class);
    }

    /**
     * Retorna as cores disponíveis dos tecidos vinculados ao produto
     */
    public function getCoresDisponiveisAttribute()
    {
        $coresDisponiveis = collect();
        
        foreach ($this->tecidos as $tecido) {
            foreach ($tecido->estoquesCores as $estoqueCor) {
                $coresDisponiveis->push([
                    'cor' => $estoqueCor->cor,
                    'codigo_cor' => $estoqueCor->codigo_cor,
                    'tecido_id' => $tecido->id,
                    'tecido_descricao' => $tecido->descricao
                ]);
            }
        }
        
        return $coresDisponiveis->unique(function ($item) {
            return $item['cor'] . '|' . $item['codigo_cor'];
        });
    }
    
    /**
     * Retorna a localização atual do produto baseada na última movimentação
     */
    public function getLocalizacaoAtualAttribute()
    {
        // Busca a última movimentação do produto ordenada pelo ID mais recente
        $ultimaMovimentacao = $this->movimentacoes()
            ->orderBy('id', 'desc')
            ->with('localizacao')
            ->first();
            
        return $ultimaMovimentacao ? $ultimaMovimentacao->localizacao : null;
    }
    
    /**
     * Relacionamento para a localização atual do produto
     * Este relacionamento é usado para eager loading da localização atual
     */
    public function localizacao_atual()
    {
        return $this->belongsTo(\App\Models\Localizacao::class, 'localizacao_atual_id');
    }
    
    /**
     * Retorna o status de conclusão atual do produto baseado na última movimentação
     */
    public function getConcluidoAtualAttribute()
    {
        // Busca a última movimentação do produto ordenada pelo ID mais recente
        $ultimaMovimentacao = $this->movimentacoes()
            ->orderBy('id', 'desc')
            ->first();
            
        return $ultimaMovimentacao ? $ultimaMovimentacao->concluido : null;
    }
    
    /**
     * Retorna a situação atual do produto baseada na última movimentação
     */
    public function getSituacaoAtualAttribute()
    {
        // Busca a última movimentação do produto ordenada pelo ID mais recente
        $ultimaMovimentacao = $this->movimentacoes()
            ->orderBy('id', 'desc')
            ->with('situacao')
            ->first();
            
        return $ultimaMovimentacao ? $ultimaMovimentacao->situacao : null;
    }
    
    /**
     * Relacionamento para a situação atual do produto
     * Este relacionamento é usado para eager loading da situação atual
     */
    public function situacao_atual()
    {
        return $this->belongsTo(\App\Models\Situacao::class, 'situacao_atual_id');
    }

    /**
     * Configuração do registro de atividades
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
