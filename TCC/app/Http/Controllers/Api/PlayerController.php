<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jogadores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// [IMPORTANTE] Adicione o cliente do Google Cloud
use Google\Cloud\Storage\StorageClient;

class PlayerController extends Controller
{
    // ... métodos index, store, show, update, destroy (Mantenha como estão) ...

    public function index(Request $request)
    {
        $query = Jogadores::with(['pessoa', 'ultima_avaliacao'])
            ->withAvg('avaliacoes as rating_medio', 'nota');

        // --- 1. FILTRO DE BUSCA (Search) ---
        if ($request->filled('search')) {
            $termo = $request->search;
            $query->where(function($q) use ($termo) {
                // Busca por nome na tabela Pessoas
                $q->whereHas('pessoa', function($q2) use ($termo) {
                    $q2->where('nome_completo', 'like', "%{$termo}%");
                })
                // Ou busca por posição na tabela Jogadores
                ->orWhere('posicao_principal', 'like', "%{$termo}%");
            });
        }

        // --- 2. FILTRO DE CATEGORIA (Sub Divisão) ---
        if ($request->filled('sub_divisao') && $request->sub_divisao !== 'Todos') {
            $sub = $request->sub_divisao;

            if ($sub === 'high-rating') {
                $query->having('rating_medio', '>=', 8.0);
            } else {
                // Mapeia a categoria para idades
                $idades = match ($sub) {
                    'Sub-7'  => [6, 7],
                    'Sub-9'  => [8, 9],
                    'Sub-11' => [10, 11],
                    'Sub-13' => [12, 13],
                    'Sub-15' => [14, 15],
                    'Sub-17' => [16, 17],
                    'Sub-20' => [18, 20], // 18, 19, 20
                    default  => null
                };

                if ($idades) {
                    // Calcula o intervalo de datas de nascimento para essas idades
                    $dataInicio = now()->subYears($idades[1] + 1)->format('Y-m-d'); // Mais velho
                    $dataFim    = now()->subYears($idades[0])->format('Y-m-d');     // Mais novo

                    $query->whereHas('pessoa', function ($q) use ($dataInicio, $dataFim) {
                        $q->whereBetween('data_nascimento', [$dataInicio, $dataFim]);
                    });
                }
            }
        }

        // Ordenação e Paginação
        $query->orderBy('rating_medio', 'desc');
        
        return $query->paginate($request->input('per_page', 12));
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

        $dadosJogador = $request->only([
            'altura_cm', 'peso_kg', 'pe_preferido', 
            'posicao_principal', 'posicao_secundaria', 'rating_medio'
        ]);

        if (!empty($dadosJogador)) {
            $jogador->update($dadosJogador);
        }

        if ($request->has('nome_completo')) {
            if ($jogador->pessoa) {
                $jogador->pessoa->update([
                    'nome_completo' => $request->input('nome_completo')
                ]);
            }
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

            // 1. Excluir Avaliações (Tabela Filha)
            \App\Models\Avaliacao::where('jogador_id', $id)->delete();

            // 2. Excluir Inscrições (Se houver, Tabela Filha)
            // \App\Models\Inscricoes::where('jogador_id', $id)->delete(); 

            // 3. Excluir Foto do GCS (Opcional, mas recomendado para não deixar lixo)
            $diskName = 'gcs';
            if ($jogador->pessoa && $jogador->pessoa->foto_perfil_url) {
                if (\Illuminate\Support\Facades\Storage::disk($diskName)->exists($jogador->pessoa->foto_perfil_url)) {
                    \Illuminate\Support\Facades\Storage::disk($diskName)->delete($jogador->pessoa->foto_perfil_url);
                }
            }

            // 4. Excluir Jogador (Tabela Pai)
            $jogador->delete();

            // 5. Excluir Pessoa (Tabela Avô - Opcional, depende da sua regra de negócio)
            /* if ($jogador->pessoa) {
                $jogador->pessoa->delete();
            }
            */

            return response()->json(['message' => 'Jogador excluído com sucesso']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir jogador',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // [MÉTODO CORRIGIDO] Upload usando SDK Direto para evitar erro de ACL
    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Máx 5MB
        ]);

        try {
            $jogador = Jogadores::with('pessoa')->findOrFail($id);

            if ($request->hasFile('image')) {
                $arquivo = $request->file('image');

                // 1. Configurações do Google Cloud (Igual ao UserController)
                $projectId = env('GOOGLE_CLOUD_PROJECT_ID');
                $bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                // IMPORTANTE: Confirme se o nome do arquivo JSON está correto aqui
                $keyFilePath = env('GOOGLE_CLOUD_KEY_FILE');

                $storage = new StorageClient([
                    'projectId' => $projectId,
                    'keyFilePath' => $keyFilePath,
                ]);

                $bucket = $storage->bucket($bucketName);

                // 2. Apagar imagem antiga se existir
                // Nota: Com SDK direto, verificamos e deletamos manualmente
                if ($jogador->pessoa->foto_perfil_url) {
                    $objetoAntigo = $bucket->object($jogador->pessoa->foto_perfil_url);
                    if ($objetoAntigo->exists()) {
                        $objetoAntigo->delete();
                    }
                }

                // 3. Preparar novo nome e Upload
                // Gera um nome único: user/timestamp_uniqid.extensao
                $nomeDoArquivo = 'user/' . time() . '_' . uniqid() . '.' . $arquivo->getClientOriginalExtension();

                // Faz o upload SEM definir ACL (evita o erro 400)
                $bucket->upload(
                    file_get_contents($arquivo->getRealPath()),
                    [
                        'name' => $nomeDoArquivo
                    ]
                );

                // 4. Atualizar no banco de dados
                if ($jogador->pessoa) {
                    $jogador->pessoa->update([
                        'foto_perfil_url' => $nomeDoArquivo
                    ]);
                }

                // 5. Gerar URL pública para retorno (opcional, se o bucket for público)
                // Se o bucket for privado, você precisaria gerar uma Signed URL, 
                // mas assumindo que segue o padrão do seu projeto:
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
}