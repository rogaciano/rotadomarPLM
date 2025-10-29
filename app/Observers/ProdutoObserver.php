<?php

namespace App\Observers;

use App\Models\Produto;

class ProdutoObserver
{
    /**
     * Handle the Produto "created" event.
     */
    public function created(Produto $produto): void
    {
        // Observer mantido para futuras implementações
    }

    /**
     * Handle the Produto "updated" event.
     */
    public function updated(Produto $produto): void
    {
        // Observer mantido para futuras implementações
    }

    /**
     * Handle the Produto "deleted" event.
     */
    public function deleted(Produto $produto): void
    {
        // Soft delete das localizações do produto
        $produto->localizacoes()->detach();
    }

    /**
     * Handle the Produto "forceDeleted" event.
     */
    public function forceDeleted(Produto $produto): void
    {
        // Delete permanente das localizações do produto
        $produto->localizacoes()->detach();
    }
}
