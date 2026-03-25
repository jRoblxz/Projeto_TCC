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

    // Exemplo de como deve ficar a lógica da sua função updateRating
    public function updateRating($jogador, array $atributos)
    {
        $observacoes = $atributos['observacoes'] ?? null;
        unset($atributos['observacoes']); // Tira o texto da conta matemática

        // Pega apenas as notas numéricas enviadas
        $notas = array_filter($atributos, function($valor) {
            return is_numeric($valor);
        });

        // Calcula a média dinâmica (soma as notas e divide pela quantidade de notas)
        $qtdNotas = count($notas);
        $media = $qtdNotas > 0 ? array_sum($notas) / $qtdNotas : 0;

        $treinador = \App\Models\Treinadores::first();
        $treinadorId = $treinador ? $treinador->id : 1; 

        // Monta os dados base
        $dadosParaSalvar = [
            'nota' => round($media, 1),
            'observacoes' => $observacoes,
            'data_avaliacao' => now(),
            'treinador_id' => $treinadorId
        ];

        // Mescla as notas específicas (Técnica, Reflexo, etc) ao array de salvamento
        foreach ($notas as $chave => $valor) {
            $dadosParaSalvar[$chave] = $valor;
        }

        $jogador->avaliacoes()->updateOrCreate(
            ['jogador_id' => $jogador->id], 
            $dadosParaSalvar
        );
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

    // Cole isso dentro do PlayerService.php
    
    public function getStats()
    {
        // 1. Busca os IDs das peneiras que estão com status "Em Andamento"
        $peneirasAtivasIds = DB::table('peneiras')
            ->whereIn('status', ['EM_ANDAMENTO', 'Em Andamento', 'Em andamento', 'em_andamento'])
            ->pluck('id');

        // 2. Busca os IDs dos jogadores inscritos APENAS nessas peneiras ativas
        $jogadoresAtivosIds = DB::table('inscricoes')
            ->whereIn('peneira_id', $peneirasAtivasIds)
            ->pluck('jogador_id')
            ->unique(); 

        // 3. Total de jogadores ativos
        $totalJogadores = $jogadoresAtivosIds->count();
        
        // 4. Agrupamento por posição
        $posicoes = Jogadores::select('posicao_principal', DB::raw('count(*) as quantidade'))
            ->whereIn('id', $jogadoresAtivosIds)
            ->whereNotNull('posicao_principal')
            ->groupBy('posicao_principal')
            ->get()
            ->map(function ($item) {
                return ['name' => $item->posicao_principal, 'quantidade' => $item->quantidade];
            });
            
        // 5. Média geral e Avaliados
        $mediaGeral = Avaliacao::whereIn('jogador_id', $jogadoresAtivosIds)->avg('nota');
        $avaliados = Avaliacao::whereIn('jogador_id', $jogadoresAtivosIds)->distinct('jogador_id')->count('jogador_id');

        // ---> CORREÇÃO DOS DESTAQUES <---
        // Usamos get() antes de contar para evitar o erro de SQL com o withAvg()
        // ---> CORREÇÃO DOS DESTAQUES <---
        $baseDestaquesQuery = Jogadores::whereIn('id', $jogadoresAtivosIds)
            ->withAvg('avaliacoes as rating_medio', 'nota')
            ->having('rating_medio', '>=', 8.0);

        // Conta DIRETO no banco de dados (rápido e não usa RAM)
        // Nota: O Laravel usa get()->count() aqui apenas para o having não quebrar o SQL nativo,
        // mas como não demos o ->with('pessoa'), é uma query extremamente leve só com IDs.
        $totalDestaques = (clone $baseDestaquesQuery)->get()->count(); 

        // Puxa APENAS os 5 melhores para a memória, já com os dados completos
        $topDestaques = (clone $baseDestaquesQuery)
            ->with('pessoa')
            ->orderByDesc('rating_medio')
            ->take(5)
            ->get();

        // 6. Inscritos por Subdivisão
        $inscritosSubdivisao = DB::table('inscricoes')
            ->join('peneiras', 'inscricoes.peneira_id', '=', 'peneiras.id')
            ->whereIn('peneiras.id', $peneirasAtivasIds)
            ->select('peneiras.sub_divisao', DB::raw('count(inscricoes.id) as quantidade'))
            ->groupBy('peneiras.sub_divisao')
            ->get()
            ->map(function ($item) {
                return ['name' => $item->sub_divisao ?: 'Geral', 'quantidade' => $item->quantidade];
            });

        return [
            'total' => $totalJogadores,
            'posicoes' => $posicoes,
            'media_geral' => round($mediaGeral ?? 0, 1),
            'total_avaliados' => $avaliados,
            'total_destaques' => $totalDestaques, // Retorna o número total para o Card
            'inscritos_subdivisao' => $inscritosSubdivisao,
            'jogadores_destaque' => $topDestaques->map(function($jogador) {
                return [
                    'id' => $jogador->id,
                    'nome_completo' => $jogador->pessoa->nome_completo,
                    'foto_perfil_url' => $jogador->pessoa->foto_perfil_url,
                    'rating_medio' => round($jogador->rating_medio ?? 0, 1)
                ];
            })
        ];
    }
}