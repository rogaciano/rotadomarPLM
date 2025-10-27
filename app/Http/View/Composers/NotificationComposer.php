<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Apenas se o usuário estiver autenticado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Carregar a localização para evitar queries extras
            $user->load('localizacao');
            
            // Contar movimentações pendentes e atrasadas
            $movimentacoesPendentes = $user->getMovimentacoesPendentesCount();
            $movimentacoesAtrasadas = $user->getMovimentacoesAtrasadasCount();
            
            $view->with([
                'notificacoesPendentes' => $movimentacoesPendentes,
                'notificacoesAtrasadas' => $movimentacoesAtrasadas,
                'temNotificacoes' => $movimentacoesPendentes > 0
            ]);
        } else {
            $view->with([
                'notificacoesPendentes' => 0,
                'notificacoesAtrasadas' => 0,
                'temNotificacoes' => false
            ]);
        }
    }
}
