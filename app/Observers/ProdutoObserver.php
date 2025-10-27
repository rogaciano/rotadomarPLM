<?php

namespace App\Observers;

use App\Models\Produto;
use App\Models\ProdutoAlocacaoMensal;

class ProdutoObserver
{
    /**
     * Handle the Produto "created" event.
     */
    public function created(Produto $produto): void
    {
        // DESABILITADO: A lógica de alocação agora é baseada em produto_localizacao
        // As alocações são criadas quando uma localização é adicionada ao produto
        // via ProdutoLocalizacaoController
    }

    /**
     * Handle the Produto "updated" event.
     */
    public function updated(Produto $produto): void
    {
        // DESABILITADO: O campo data_prevista_faccao foi movido para produto_localizacao
        // A lógica de alocação agora é gerenciada via ProdutoLocalizacao
    }

    /**
     * Criar alocação inicial ao criar produto
     * @deprecated - Lógica movida para produto_localizacao
     */
    private function criarAlocacaoInicial(Produto $produto)
    {
        // DESABILITADO: Campo data_prevista_faccao não existe mais em produtos
        // Alocações são criadas via produto_localizacao
    }

    /**
     * Atualizar alocação ao atualizar produto
     * @deprecated - Lógica movida para produto_localizacao
     */
    private function atualizarAlocacao(Produto $produto)
    {
        // DESABILITADO: Campo data_prevista_faccao não existe mais em produtos
        // Alocações são gerenciadas via produto_localizacao
    }

    /**
     * Handle the Produto "deleted" event.
     */
    public function deleted(Produto $produto): void
    {
        // Soft delete das alocações
        ProdutoAlocacaoMensal::where('produto_id', $produto->id)->delete();
    }

    /**
     * Handle the Produto "forceDeleted" event.
     */
    public function forceDeleted(Produto $produto): void
    {
        // Delete permanente das alocações
        ProdutoAlocacaoMensal::where('produto_id', $produto->id)->forceDelete();
    }
}
