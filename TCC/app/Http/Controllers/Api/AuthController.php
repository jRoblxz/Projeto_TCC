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

        // 4. Limpar tokens antigos (Opcional: garante apenas 1 sessão por vez)
        // $user->tokens()->delete();

        // 5. Criar Novo Token (Sanctum)
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
            ]
        ], 200);
    }

    /**
     * Retorna dados do usuário logado
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout: Revoga o token atual
     */
    public function logout(Request $request)
    {
        // Deleta apenas o token que foi usado na requisição atual
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Deslogado com sucesso']);
    }
    
    /**
     * Refresh (Sanctum não tem refresh automático igual JWT, 
     * mas você pode criar um novo token se o atual ainda for válido)
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete(); // Remove o atual
        
        $newToken = $user->createToken('api_token_refresh')->plainTextToken;
        
        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'Bearer',
        ]);
    }
}