<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Use o seu modelo de usuário
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Cria uma nova instância do AuthController.
     * Define o middleware de autenticação da API.
     */
    public function __construct()
    {
        // O 'auth:api' protege todas as rotas deste controller,
        // exceto 'login' e 'register' (se você tiver um)
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Tenta logar o usuário e (se sucesso) retorna o token JWT.
     */
    public function login(Request $request)
    {
        // 1. Valida os dados que chegam
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Tenta autenticar
        $credentials = $request->only('email', 'password');

        // [IMPORTANTE] Use 'auth("api")'
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        // 3. Se deu certo, retorna o token formatado
        return $this->respondWithToken($token);
    }

    /**
     * Retorna os dados do usuário autenticado (rota protegida).
     */
    public function me()
    {
        // auth("api") garante que estamos pegando o usuário do token
        return response()->json(auth('api')->user());
    }

    /**
     * Desloga o usuário (invalida o token).
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Deslogado com sucesso']);
    }

    /**
     * Atualiza um token.
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Helper para formatar a resposta do token.
     */
    protected function respondWithToken($token)
    {
        $user = auth('api')->user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [ // Enviamos os dados do usuário junto
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }
}