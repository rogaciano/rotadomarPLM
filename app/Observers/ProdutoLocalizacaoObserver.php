<?php

namespace App\Observers;

use App\Models\ProdutoLocalizacao;
use App\Models\ProdutoAlocacaoMensal;
use Illuminate\Support\Facades\Log;

class ProdutoLocalizacaoObserver
{
    /**
     * Handle the ProdutoLocalizacao "created" event.
     * Cria alocação mensal quando uma localização é adicionada ao produto
     */
    public function created(ProdutoLocalizacao $produtoLocalizacao): void
    {
        $this->criarAlocacaoMensal($produtoLocalizacao);
    }

    /**
     * Handle the ProdutoLocalizacao "updated" event.
     * Atualiza alocação mensal quando dados são alterados
     */
    public function updated(ProdutoLocalizacao $produtoLocalizacao): void
    {
        // Se mudou quantidade, data ou localização, atualiza alocação
        if ($produtoLocalizacao->isDirty(['quantidade', 'data_prevista_faccao', 'localizacao_id'])) {
            $this->atualizarAlocacaoMensal($produtoLocalizacao);
        }
    }

    /**
     * Handle the ProdutoLocalizacao "deleted" event.
     * Remove alocação mensal quando localização é removida do produto
     */
    public function deleted(ProdutoLocalizacao $produtoLocalizacao): void
    {
        $this->removerAlocacaoMensal($produtoLocalizacao);
    }

    /**
     * Criar alocação mensal baseada em produto_localizacao
     */
    private function criarAlocacaoMensal(ProdutoLocalizacao $produtoLocalizacao)
    {
        // Só cria se tiver data prevista de facção
        if (!$produtoLocalizacao->data_prevista_faccao || $produtoLocalizacao->quantidade <= 0) {
            return;
        }

        try {
            // Verificar se já existe alocação para este produto/localização/mês
            $dataFaccao = is_string($produtoLocalizacao->data_prevista_faccao) 
                ? \Carbon\Carbon::parse($produtoLocalizacao->data_prevista_faccao)
                : $produtoLocalizacao->data_prevista_faccao;

            $alocacaoExistente = ProdutoAlocacaoMensal::where('produto_id', $produtoLocalizacao->produto_id)
                ->where('localizacao_id', $produtoLocalizacao->localizacao_id)
                ->where('mes', $dataFaccao->month)
                ->where('ano', $dataFaccao->year)
                ->where('produto_localizacao_id', $produtoLocalizacao->id)
                ->first();

            if (!$alocacaoExistente) {
                ProdutoAlocacaoMensal::create([
                    'produto_id' => $produtoLocalizacao->produto_id,
                    'localizacao_id' => $produtoLocalizacao->localizacao_id,
                    'mes' => $dataFaccao->month,
                    'ano' => $dataFaccao->year,
                    'quantidade' => $produtoLocalizacao->quantidade,
                    'tipo' => 'original',
                    'usuario_id' => auth()->id() ?? 1,
                    'produto_localizacao_id' => $produtoLocalizacao->id,
                    'ordem_producao' => $produtoLocalizacao->ordem_producao,
                    'observacoes' => $produtoLocalizacao->observacao ?? 'Alocação automática via produto_localizacao'
                ]);

                Log::info("Alocação mensal criada", [
                    'produto_id' => $produtoLocalizacao->produto_id,
                    'localizacao_id' => $produtoLocalizacao->localizacao_id,
                    'ordem_producao' => $produtoLocalizacao->ordem_producao
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao criar alocação mensal: " . $e->getMessage(), [
                'produto_localizacao_id' => $produtoLocalizacao->id
            ]);
        }
    }

    /**
     * Atualizar alocação mensal baseada em produto_localizacao
     */
    private function atualizarAlocacaoMensal(ProdutoLocalizacao $produtoLocalizacao)
    {
        try {
            // Buscar alocação vinculada a este produto_localizacao
            $alocacao = ProdutoAlocacaoMensal::where('produto_localizacao_id', $produtoLocalizacao->id)
                ->first();

            if ($alocacao) {
                // Se não tem mais data prevista, remove alocação
                if (!$produtoLocalizacao->data_prevista_faccao) {
                    $alocacao->delete();
                    Log::info("Alocação mensal removida (sem data)", [
                        'produto_localizacao_id' => $produtoLocalizacao->id
                    ]);
                    return;
                }

                // Atualizar dados da alocação
                $dataFaccao = is_string($produtoLocalizacao->data_prevista_faccao) 
                    ? \Carbon\Carbon::parse($produtoLocalizacao->data_prevista_faccao)
                    : $produtoLocalizacao->data_prevista_faccao;

                $alocacao->update([
                    'localizacao_id' => $produtoLocalizacao->localizacao_id,
                    'mes' => $dataFaccao->month,
                    'ano' => $dataFaccao->year,
                    'quantidade' => $produtoLocalizacao->quantidade,
                    'ordem_producao' => $produtoLocalizacao->ordem_producao,
                    'observacoes' => ($produtoLocalizacao->observacao ?? '') . ' | Atualizado em ' . now()->format('d/m/Y H:i')
                ]);

                Log::info("Alocação mensal atualizada", [
                    'produto_localizacao_id' => $produtoLocalizacao->id
                ]);
            } else {
                // Se não existe alocação, cria uma nova
                $this->criarAlocacaoMensal($produtoLocalizacao);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar alocação mensal: " . $e->getMessage(), [
                'produto_localizacao_id' => $produtoLocalizacao->id
            ]);
        }
    }

    /**
     * Remover alocação mensal vinculada
     */
    private function removerAlocacaoMensal(ProdutoLocalizacao $produtoLocalizacao)
    {
        try {
            // Buscar por produto_localizacao_id (método preferencial)
            $alocacoes = ProdutoAlocacaoMensal::where('produto_localizacao_id', $produtoLocalizacao->id)->get();
            
            // Se não encontrou, buscar por produto_id + localizacao_id + ordem_producao (fallback para registros antigos)
            if ($alocacoes->isEmpty() && $produtoLocalizacao->data_prevista_faccao) {
                $dataFaccao = is_string($produtoLocalizacao->data_prevista_faccao) 
                    ? \Carbon\Carbon::parse($produtoLocalizacao->data_prevista_faccao)
                    : $produtoLocalizacao->data_prevista_faccao;
                
                $alocacoes = ProdutoAlocacaoMensal::where('produto_id', $produtoLocalizacao->produto_id)
                    ->where('localizacao_id', $produtoLocalizacao->localizacao_id)
                    ->where('mes', $dataFaccao->month)
                    ->where('ano', $dataFaccao->year)
                    ->where(function($q) use ($produtoLocalizacao) {
                        $q->whereNull('produto_localizacao_id')
                          ->orWhere('ordem_producao', $produtoLocalizacao->ordem_producao);
                    })
                    ->get();
            }

            // Deletar todas as alocações encontradas
            $removidas = 0;
            foreach($alocacoes as $alocacao) {
                $alocacao->delete();
                $removidas++;
                
                Log::info("Alocação mensal removida", [
                    'produto_localizacao_id' => $produtoLocalizacao->id,
                    'alocacao_id' => $alocacao->id,
                    'metodo' => $alocacao->produto_localizacao_id ? 'pivot_id' : 'fallback'
                ]);
            }
            
            if ($removidas == 0) {
                Log::warning("Nenhuma alocação mensal encontrada para remover", [
                    'produto_localizacao_id' => $produtoLocalizacao->id,
                    'produto_id' => $produtoLocalizacao->produto_id,
                    'localizacao_id' => $produtoLocalizacao->localizacao_id
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao remover alocação mensal: " . $e->getMessage(), [
                'produto_localizacao_id' => $produtoLocalizacao->id
            ]);
        }
    }
}
