<?php

namespace App\Services;

use App\Models\Jogadores;
use App\Models\Avaliacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Log;

class PlayerService
{
    public function updatePlayer($id, array $data, $file = null)
    {
        return DB::transaction(function () use ($id, $data, $file) {
            $jogador = Jogadores::with('pessoa')->findOrFail($id);

            // 1. Handle Image Upload (Direct GCS SDK)
            if ($file) {
                $diskName = 'gcs'; // Or your custom logic
                // ... (Insert your existing GCS upload logic here) ...
                // For brevity, assuming $path is returned
                $path = $file->store('user', $diskName); 
                $jogador->pessoa->update(['foto_perfil_url' => $path]);
            }

            // 2. Update Person Data
            $jogador->pessoa->update([
                'nome_completo' => $data['nome_completo'] ?? $jogador->pessoa->nome_completo,
                // Add other fields...
            ]);

            // 3. Update Rating Logic
            if (isset($data['rating_medio'])) {
                $this->updateRating($jogador, $data['rating_medio']);
            }

            // 4. Update Player Data
            $jogador->update([
                'altura_cm' => $data['altura_cm'] ?? $jogador->altura_cm,
                'peso_kg' => $data['peso_kg'] ?? $jogador->peso_kg,
                'posicao_principal' => $data['posicao_principal'] ?? $jogador->posicao_principal,
                // ...
            ]);

            return $jogador->fresh(['pessoa']);
        });
    }

    private function updateRating($jogador, $score)
    {
        // Reusing your existing logic nicely
        $avaliacao = Avaliacao::where('jogador_id', $jogador->id)
            ->orderByDesc('data_avaliacao')->first();

        if ($avaliacao) {
            $avaliacao->update(['nota' => $score, 'data_avaliacao' => now()]);
        } else {
            Avaliacao::create([
                'jogador_id' => $jogador->id,
                'treinador_id' => Auth::id() ?? 1,
                'nota' => $score,
                'observacoes' => 'Rating editado via API',
                'data_avaliacao' => now(),
            ]);
        }
    }

    public function getAllWithFilters($perPage, $filters)
    {
        $query = Jogadores::with(['pessoa', 'ultima_avaliacao'])
            ->withAvg('avaliacoes as rating_medio', 'nota');

        // Filtro de Busca
        if (!empty($filters['search'])) {
            $termo = $filters['search'];
            $query->where(function($q) use ($termo) {
                $q->whereHas('pessoa', function($q2) use ($termo) {
                    $q2->where('nome_completo', 'like', "%{$termo}%");
                })->orWhere('posicao_principal', 'like', "%{$termo}%");
            });
        }

        // Filtro de Categoria (Sub-XX)
        if (!empty($filters['sub_divisao']) && $filters['sub_divisao'] !== 'Todos') {
            $sub = $filters['sub_divisao'];
            
            if ($sub === 'high-rating') {
                $query->having('rating_medio', '>=', 8.0);
            } else {
                $idades = match ($sub) {
                    'Sub-7'  => [6, 7],
                    'Sub-9'  => [8, 9],
                    'Sub-11' => [10, 11],
                    'Sub-13' => [12, 13],
                    'Sub-15' => [14, 15],
                    'Sub-17' => [16, 17],
                    'Sub-20' => [18, 20],
                    default  => null
                };

                if ($idades) {
                    $dataInicio = now()->subYears($idades[1] + 1)->format('Y-m-d');
                    $dataFim    = now()->subYears($idades[0])->format('Y-m-d');
                    $query->whereHas('pessoa', fn($q) => $q->whereBetween('data_nascimento', [$dataInicio, $dataFim]));
                }
            }
        }

        $query->orderBy('rating_medio', 'desc');
        return $query->paginate($perPage);
    }
}