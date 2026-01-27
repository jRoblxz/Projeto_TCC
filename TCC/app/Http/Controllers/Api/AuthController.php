<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login: Valida credenciais e cria o Token (Sanctum)
     */
    public function login(Request $request)
    {
        // 1. Validação
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Buscar Usuário
        $user = User::where('email', $request->email)->first();

        // 3. Verificar Senha (Hash)
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciais incorretas (E-mail ou senha inválidos).'
            ], 401);
        }

        // 4. Carregar dados do Jogador e da Pessoa vinculados (Se existirem)
        // Isso previne o erro de ID incorreto no frontend
        $user->load('pessoa.jogador');

        // 5. Extrair jogador_id
        $jogadorId = null;
        if ($user->pessoa && $user->pessoa->jogador) {
            $jogadorId = $user->pessoa->jogador->id;
        }

        // 6. Criar Novo Token (Sanctum)
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'jogador_id' => $jogadorId, // ID correto recuperado via Pessoa
                'pessoa' => $user->pessoa,  // Dados da pessoa
            ]
        ], 200);
    }


    /**
     * Retorna dados do usuário logado
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('pessoa.jogador'); // Carrega a cadeia

        $userData = $user->toArray();

        // Injeta o jogador_id manualmente na resposta
        $userData['jogador_id'] = null;
        if ($user->pessoa && $user->pessoa->jogador) {
            $userData['jogador_id'] = $user->pessoa->jogador->id;
        }

        return response()->json($userData);
    }

    /**
     * Logout: Revoga o token atual
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Deslogado com sucesso']);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $newToken = $user->createToken('api_token_refresh')->plainTextToken;

        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'Bearer',
        ]);
    }
}
