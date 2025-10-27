<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission, string $action = null): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Quando uma ação é informada (create, read, update, delete), verifica permissão por ação
        if ($action !== null) {
            if (!$user->canAction($action, $permission)) {
                return redirect()->back()->with('error', 'Você não tem permissão para acessar este recurso.');
            }
        } else if (!$user->hasPermission($permission)) {
            return redirect()->back()->with('error', 'Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}
