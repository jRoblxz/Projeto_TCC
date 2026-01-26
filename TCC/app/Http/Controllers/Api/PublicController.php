<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peneiras;
use App\Models\Pessoas;
use App\Models\Jogadores;
use App\Models\Inscricoes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
// [ADICIONADO] Import do Client do Google
use Google\Cloud\Storage\StorageClient; 

class PublicController extends Controller
{
    // Listar peneiras abertas
    public function getOpenPeneiras()
    {
        $peneiras = Peneiras::whereIn('status', ['AGENDADA', 'EM_ANDAMENTO'])
            ->select('id', 'title', 'date', 'subdivision', 'status') // Ajustado para os nomes corretos do seu banco
            ->orderByDesc('date')
            ->get();
            
        return response()->json($peneiras);
    }

    public function registerCandidate(Request $request)
    {
        // 1. Validação
        $validated = $request->validate([
            'nome_completo' => 'required|string',
            'email' => 'required|email|unique:pessoas,email',
            'cpf' => 'required|string|unique:pessoas,cpf',
            'peneira_id' => 'required|exists:peneiras,id',
            'foto_perfil_url' => 'nullable|file|image|max:5120', // Validação da imagem
        ]);

        DB::beginTransaction();
        try {
            $path = null;

            // 2. Lógica de Upload (Copiada do PlayerController e Adaptada)
            if ($request->hasFile('foto_perfil_url')) {
                $arquivo = $request->file('foto_perfil_url');

                // Configurações do Google Cloud (Mesmas do PlayerController)
                $projectId = env('GOOGLE_CLOUD_PROJECT_ID');
                $bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                // IMPORTANTE: Verifique se este nome de arquivo json é o mesmo usado no PlayerController
                $keyFilePath = env('GOOGLE_CLOUD_KEY_FILE');

                $storage = new StorageClient([
                    'projectId' => $projectId,
                    'keyFilePath' => $keyFilePath,
                ]);

                $bucket = $storage->bucket($bucketName);

                // Gera nome único: user/timestamp_uniqid.extensao
                $nomeDoArquivo = 'user/' . time() . '_' . uniqid() . '.' . $arquivo->getClientOriginalExtension();

                // Faz o upload SEM definir ACL (evita erro 400)
                $bucket->upload(
                    file_get_contents($arquivo->getRealPath()),
                    [
                        'name' => $nomeDoArquivo
                    ]
                );

                // Salva apenas o caminho relativo, pois seu Model Pessoas já tem o Accessor para a URL completa
                $path = $nomeDoArquivo;
            }

            // 3. Criações
            $pessoa = Pessoas::create([
                'nome_completo' => $request->nome_completo,
                'email' => $request->email,
                'cpf' => $request->cpf,
                // Adicione os outros campos do form (RG, telefone, cidade, etc)
                'rg' => $request->rg ?? null, 
                'telefone' => $request->telefone ?? null,
                'cidade' => $request->cidade ?? null,
                'data_nascimento' => $request->data_nascimento,
                'foto_perfil_url' => $path // Agora o path está preenchido corretamente
            ]);

            $jogador = Jogadores::create([
                'pessoa_id' => $pessoa->id,
                'posicao_principal' => $request->posicao_principal,
                'posicao_secundaria' => $request->posicao_secundaria ?? null,
                'pe_preferido' => $request->pe_preferido ?? null,
                'altura_cm' => $request->altura_cm ?? null,
                'peso_kg' => $request->peso_kg ?? null,
                'historico_lesoes_cirurgias' => $request->historico_lesoes_cirurgias ?? 'nao',
                'video_apresentacao_url' => $request->video_apresentacao_url ?? null,
            ]);

            Inscricoes::create([
                'jogador_id' => $jogador->id,
                'peneira_id' => $request->peneira_id,
                'data_inscricao' => now(),
            ]);

            // Cria usuário para login futuro
            User::create([
                'name' => $pessoa->nome_completo,
                'email' => $pessoa->email,
                'password' => Hash::make($request->cpf), // Senha padrão = CPF
                'role' => 'candidato',
            ]);

            DB::commit();
            return response()->json(['message' => 'Inscrição realizada com sucesso!'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Esse log vai salvar o erro real em storage/logs/laravel.log
            Log::error("Erro no registro: " . $e->getMessage());
            Log::error($e->getTraceAsString()); // Loga a linha exata do erro
            
            // Retorna o erro real para facilitar seu debug (Remova isso em produção)
            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
}