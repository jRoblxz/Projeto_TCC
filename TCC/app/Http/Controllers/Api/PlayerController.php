<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jogadores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Storage\StorageClient;
use App\Services\PlayerService;

class PlayerController extends Controller
{
    protected $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'sub_divisao' => $request->sub_divisao
        ];

        $players = $this->playerService->getAllWithFilters(
            $request->input('per_page', 12), 
            $filters
        );

        return response()->json($players);
    }
    
    public function show($id)
    {
        $jogador = \App\Models\Jogadores::with(['pessoa', 'ultima_avaliacao'])
            ->withAvg('avaliacoes as rating_calculado', 'nota')
            ->findOrFail($id);

        if (!$jogador->rating_medio || $jogador->rating_medio == 0) {
            $jogador->rating_medio = $jogador->rating_calculado ?? 0;
        }

        return response()->json($jogador);
    }

    public function update(Request $request, $id)
    {
        $jogador = \App\Models\Jogadores::with('pessoa')->findOrFail($id);
        
        // Atualiza apenas os dados normais do jogador
        $dadosJogador = $request->only([
            'altura_cm', 'peso_kg', 'pe_preferido',
            'posicao_principal', 'posicao_secundaria'
        ]);

        if (!empty($dadosJogador)) {
            $jogador->update($dadosJogador);
        }

        // Atualiza o nome se foi enviado
        if ($request->has('nome_completo')) {
            if ($jogador->pessoa) {
                $jogador->pessoa->update([
                    'nome_completo' => $request->input('nome_completo')
                ]);
            }
        }

        // ==========================================
        // CORREÇÃO: ENVIANDO O PENEIRA_ID PARA O SERVICE
        // ==========================================
        if ($request->has('atributos')) {
            // Capturamos o peneira_id que vem do React
            $peneiraId = $request->input('peneira_id'); 
            
            // Passamos o peneira_id como terceiro parâmetro para a função updateRating!
            $this->playerService->updateRating($jogador, $request->input('atributos'), $peneiraId);
        }

        return response()->json([
            'message' => 'Atualizado com sucesso',
            'data' => $jogador->refresh()
        ]);
    }

    public function destroy($id)
    {
        try {
            $jogador = Jogadores::with('pessoa')->findOrFail($id);
            \App\Models\Avaliacao::where('jogador_id', $id)->delete();
            
            $diskName = 'gcs';
            if ($jogador->pessoa && $jogador->pessoa->foto_perfil_url) {
                if (\Illuminate\Support\Facades\Storage::disk($diskName)->exists($jogador->pessoa->foto_perfil_url)) {
                    \Illuminate\Support\Facades\Storage::disk($diskName)->delete($jogador->pessoa->foto_perfil_url);
                }
            }
            $jogador->delete();
            return response()->json(['message' => 'Jogador excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro', 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        try {
            $jogador = Jogadores::with('pessoa')->findOrFail($id);

            if ($request->hasFile('image')) {
                $arquivo = $request->file('image');
                $projectId = env('GOOGLE_CLOUD_PROJECT_ID');
                $bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                $keyFilePath = env('GOOGLE_CLOUD_KEY_FILE');

                $storage = new StorageClient([
                    'projectId' => $projectId,
                    'keyFilePath' => $keyFilePath,
                ]);

                $bucket = $storage->bucket($bucketName);

                if ($jogador->pessoa->foto_perfil_url) {
                    $objetoAntigo = $bucket->object($jogador->pessoa->foto_perfil_url);
                    if ($objetoAntigo->exists()) {
                        $objetoAntigo->delete();
                    }
                }

                $nomeDoArquivo = 'user/' . time() . '_' . uniqid() . '.' . $arquivo->getClientOriginalExtension();

                $bucket->upload(
                    file_get_contents($arquivo->getRealPath()),
                    ['name' => $nomeDoArquivo]
                );

                if ($jogador->pessoa) {
                    $jogador->pessoa->update(['foto_perfil_url' => $nomeDoArquivo]);
                }

                $url = "https://storage.googleapis.com/{$bucketName}/{$nomeDoArquivo}";

                return response()->json([
                    'message' => 'Foto atualizada com sucesso!',
                    'path' => $nomeDoArquivo,
                    'url' => $url
                ]);
            }
            return response()->json(['message' => 'Nenhum arquivo enviado.'], 400);
        } catch (\Exception $e) {
            Log::error("Erro no upload GCS (SDK): " . $e->getMessage());
            return response()->json(['message' => 'Erro ao salvar imagem: ' . $e->getMessage()], 500);
        }
    }

    public function stats()
    {
        $estatisticas = $this->playerService->getStats();
        return response()->json($estatisticas);
    }
}