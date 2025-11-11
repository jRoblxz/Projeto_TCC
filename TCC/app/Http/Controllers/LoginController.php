<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class LoginController extends BaseController
{
    /**
     * Mostra a página de login.
     */
    public function showLoginForm()
    {
        // O nome da sua view de login
        return view('login'); 
    }

    /**
     * Processa a tentativa de login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Tenta autenticar o usuário (criar a SESSÃO)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Pega o usuário que acabou de logar
            $user = Auth::user();

            // Redireciona com base no 'role'
            if ($user->role === 'adm') {
                return redirect()->intended('home'); // Admin vai para /home
            } 
            elseif ($user->role === 'candidato') {
                return redirect()->route('jogadores.index'); // Candidato vai para /players
            }
            // --- [FIM DA CORREÇÃO] ---

            // Um fallback, caso o usuário não tenha role
            return redirect('/');
        }

        // Falhou! Volta para o login com uma mensagem de erro
        return back()->withErrors([
            'email' => 'As credenciais fornecidas não batem.',
        ])->onlyInput('email');
    }

    /**
     * Desloga o usuário (destrói a SESSÃO).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redireciona para a raiz
    }
}