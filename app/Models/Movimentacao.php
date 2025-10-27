<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Movimentacao extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'movimentacoes';

    protected $fillable = [
        'comprometido',
        'produto_id',
        'localizacao_id',
        'data_entrada',
        'data_saida',
        'data_devolucao',
        'tipo_id',
        'situacao_id',
        'observacao',
        'anexo',
        'concluido'
    ];

    protected $casts = [
        'data_entrada' => 'datetime',
        'data_saida' => 'datetime',
        'data_devolucao' => 'datetime',
        'comprometido' => 'integer',
        'concluido' => 'boolean'
    ];

    // Definindo relacionamentos para serem carregados automaticamente
    protected $with = ['produto', 'localizacao', 'tipo', 'situacao'];

    // Accessor para URL do anexo
    public function getAnexoUrlAttribute()
    {
        if (!$this->anexo) {
            return null;
        }

        // Se caminho começa com uploads/, usar asset direto
        if (\Illuminate\Support\Str::startsWith($this->anexo, 'uploads/')) {
            return asset($this->anexo);
        }

        // Se for um caminho de rede (começa com // ou \\)
        if (\Illuminate\Support\Str::startsWith($this->anexo, '//') || \Illuminate\Support\Str::startsWith($this->anexo, '\\\\')) {
            // Converter para uma URL válida - usar uma rota específica para servir arquivos de rede
            // Codificar o caminho para evitar problemas com caracteres especiais na URL
            $encodedPath = urlencode($this->anexo);
            return route('arquivo.rede', ['path' => $encodedPath]);
        }

        // Caso contrário, assumir que está no disco public
        return \Illuminate\Support\Facades\Storage::url($this->anexo);
    }

    // Relacionamentos
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id')->withTrashed();
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class)->withTrashed();
    }

    public function tipo()
    {
        return $this->belongsTo(Tipo::class)->withTrashed();
    }

    public function situacao()
    {
        return $this->belongsTo(Situacao::class)->withTrashed();
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
