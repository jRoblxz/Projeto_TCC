<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Treinadores; // <-- IMPORTANTE: Model de treinadores adicionada
use Illuminate\Support\Facades\Validator;
use App\Models\Pessoas; // Adicione esta linha lá em cima
use Google\Cloud\Storage\StorageClient;

class AuthController extends Controller
{
    /**
     * Registro: Cria novos usuários (Admins ou Treinadores)
     */
    /**
     * Registro: Cria novos usuários (Admins ou Treinadores)
     */
    /**
     * Registro: Cria novos usuários (Admins ou Treinadores)
     */
    /**
     * Registro: Cria novos usuários (Admins ou Treinadores)
     */
    public function register(Request $request)
    {
        // 1. Validação dos dados (incluindo foto e campos opcionais do treinador)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:adm,treinador', 
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            // Campos opcionais do treinador
            'clube_organizacao' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'cref' => 'nullable|string|max:255',
            'anos_experiencia' => 'nullable|integer',
            'biografia_resumo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        try {
            // 2. Cria o usuário de acesso na tabela 'users'
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // 3. Upload da foto (Direto pelo Google Cloud Storage SDK)
            $fotoPath = null;
            if ($request->hasFile('foto_perfil')) {
                $arquivo = $request->file('foto_perfil');
                
                $projectId = env('GOOGLE_CLOUD_PROJECT_ID');
                $bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                $keyFilePath = env('GOOGLE_CLOUD_KEY_FILE');

                $storage = new StorageClient([
                    'projectId' => $projectId,
                    'keyFilePath' => $keyFilePath,
                ]);

                $bucket = $storage->bucket($bucketName);
                
                // Cria um nome único para o arquivo
                $nomeDoArquivo = 'user/' . time() . '_' . uniqid() . '.' . $arquivo->getClientOriginalExtension();

                // Faz o upload 
                $bucket->upload(
                    file_get_contents($arquivo->getRealPath()),
                    ['name' => $nomeDoArquivo]
                );

                $fotoPath = $nomeDoArquivo;
            }

            // 4. Cria os dados pessoais na tabela 'pessoas'
            // REMOVIDO o user_id daqui! A ligação será feita pelo e-mail automaticamente.
            $pessoa = Pessoas::create([
                'nome_completo' => $request->name,
                'email' => $request->email,
                'foto_perfil_url' => $fotoPath,
            ]);

            // 5. Se for treinador, vincula os campos extras na tabela 'treinadores'
            if ($request->role === 'treinador') {
                Treinadores::create([
                    'pessoa_id' => $pessoa->id,
                    'clube_organizacao' => $request->clube_organizacao,
                    'cargo' => $request->cargo,
                    'cref' => $request->cref,
                    'anos_experiencia' => $request->anos_experiencia,
                    'biografia_resumo' => $request->biografia_resumo,
                ]);
            }

            return response()->json([
                'message' => 'Usuário criado com sucesso!',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao salvar usuário: ' . $e->getMessage()], 500);
        }
    }

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
                'isAdmin' => in_array($user->role, ['adm', 'treinador']),
            ]
        ], 200);
    }


    /**
     * Retorna dados do usuário logado
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('pessoa.jogador'); 

        $userData = $user->toArray();
        $userData['jogador_id'] = $user->pessoa && $user->pessoa->jogador ? $user->pessoa->jogador->id : null;
        
        // ADICIONE ESTA LINHA:
        $userData['isAdmin'] = in_array($user->role, ['adm', 'treinador']);

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