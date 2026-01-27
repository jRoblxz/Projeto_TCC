<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // 1. Verifica se tem usuário logado e se o papel (role) é o correto
        if (! $request->user() || $request->user()->role !== $role) {
            
            // --- [CORREÇÃO] ---
            // Em vez de deslogar ou redirecionar, retornamos um erro 403 (Proibido).
            // Assim, o Frontend sabe que o usuário existe, mas não pode mexer aqui.
            return response()->json([
                'message' => 'Acesso negado. Você não tem permissão de ' . $role
            ], 403);
        }

        // 2. Se passou na verificação, deixa a requisição continuar
        return $next($request);
    }
}