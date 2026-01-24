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
}