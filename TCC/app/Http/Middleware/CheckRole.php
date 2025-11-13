<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe o Auth

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
        // Verifica se o usuário está logado
        if (!Auth::check()) {
            return redirect()->route('login'); 
        }

        // Pega o usuário da sessão
        $user = Auth::user();

        // Verifica o 'role'
        if ($user->role !== $role) {
            
            // --- [CORREÇÃO DO LOOP] ---
            // Se o role não bate, desloga o usuário e o envia
            // para a tela de login com uma mensagem de erro.
            // Isso previne qualquer loop.
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                             ->with('error', 'Acesso não permitido. Suas permissões não são suficientes.');
            // --- [FIM DA CORREÇÃO] ---
        }
        
        // Se passou, continua
        return $next($request);
    }
}