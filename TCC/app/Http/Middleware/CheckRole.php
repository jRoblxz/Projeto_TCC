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
     * @param  string  ...$roles (Agora aceita múltiplas roles)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // 1. Verifica se tem usuário logado
        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        // 2. Verifica se a role do usuário está dentro da lista de roles permitidas
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Acesso negado. Você não tem permissão de adm'
            ], 403);
        }

        // 3. Se passou na verificação, deixa a requisição continuar
        return $next($request);
    }
}