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
                
            ]);

            // 3. Update Rating Logic (AGORA COM A PENEIRA!)
            if (isset($data['rating_medio'])) {
                // Pega a peneira enviada pelo Frontend. Se não enviar, usa null.
                $peneiraId = $data['peneira_id'] ?? null; 
                $this->updateRating($jogador, $data['rating_medio'], $peneiraId);
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

    public function updateRating($jogador, array $atributos, $peneiraId = null)
    {
        $observacoes = $atributos['observacoes'] ?? null;
        unset($atributos['observacoes']); 

        $notas = array_filter($atributos, function($valor) {
            return is_numeric($valor);
        });

        $qtdNotas = count($notas);
        $media = $qtdNotas > 0 ? array_sum($notas) / $qtdNotas : 0;

        $treinador = \App\Models\Treinadores::first();
        $treinadorId = $treinador ? $treinador->id : 1; 

        $dadosParaSalvar = [
            'nota' => round($media, 1),
            'observacoes' => $observacoes,
            'data_avaliacao' => now(),
            'treinador_id' => $treinadorId
        ];

        foreach ($notas as $chave => $valor) {
            $dadosParaSalvar[$chave] = $valor;
        }

        // ==========================================
        // CORREÇÃO CRÍTICA AQUI:
        // ==========================================
        $busca = [
            'jogador_id' => $jogador->id,
            'peneira_id' => $peneiraId // <--- AGORA ELE BUSCA O PAR JOGADOR+PENEIRA
        ];

        if ($peneiraId) {
            $dadosParaSalvar['peneira_id'] = $peneiraId;
        }

        $jogador->avaliacoes()->updateOrCreate(
            $busca, 
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
    
    public function getStats($filtroSub = null) 
    {
        // 1. Prepara a query base das peneiras ativas
        $queryPeneiras = DB::table('peneiras')
            ->whereIn('status', ['EM_ANDAMENTO', 'Em Andamento', 'Em andamento', 'em_andamento']);

        // 2. Se o filtro chegou do React, nós aplicamos ele
        if ($filtroSub) {
            $queryPeneiras->where('sub_divisao', $filtroSub);
        }

        // 3. Pega os IDs (já com o filtro aplicado!)
        $peneirasAtivasIds = $queryPeneiras->pluck('id');

        // 4. Busca os IDs dos jogadores inscritos APENAS nessas peneiras
        $jogadoresAtivosIds = DB::table('inscricoes')
            ->whereIn('peneira_id', $peneirasAtivasIds)
            ->pluck('jogador_id')
            ->unique(); 

        // 5. Total de jogadores ativos
        $totalJogadores = $jogadoresAtivosIds->count();
        
        // 6. Agrupamento por posição
        $posicoes = Jogadores::select('posicao_principal', DB::raw('count(*) as quantidade'))
            ->whereIn('id', $jogadoresAtivosIds)
            ->whereNotNull('posicao_principal')
            ->groupBy('posicao_principal')
            ->get()
            ->map(function ($item) {
                return ['name' => $item->posicao_principal, 'quantidade' => $item->quantidade];
            });
            
        // 7. Média geral e Avaliados
        $mediaGeral = Avaliacao::whereIn('jogador_id', $jogadoresAtivosIds)->avg('nota');
        $avaliados = Avaliacao::whereIn('jogador_id', $jogadoresAtivosIds)->distinct('jogador_id')->count('jogador_id');

        // 8. Base dos Destaques
        $baseDestaquesQuery = Jogadores::whereIn('id', $jogadoresAtivosIds)
            ->withAvg('avaliacoes as rating_medio', 'nota')
            ->having('rating_medio', '>=', 8.0);

        $totalDestaques = (clone $baseDestaquesQuery)->get()->count(); 

        $totalAprovados = DB::table('inscricoes')
            ->whereIn('peneira_id', $peneirasAtivasIds)
            ->where('status', 'aprovado')
            ->count();

        // 9. Puxa os 5 melhores
        $topDestaques = (clone $baseDestaquesQuery)
            ->with('pessoa')
            ->orderByDesc('rating_medio')
            ->take(5)
            ->get();

        // 10. Inscritos por Subdivisão
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
            'total_destaques' => $totalDestaques,
            'inscritos_subdivisao' => $inscritosSubdivisao,
            'funil_conversao' => [
                [
                    'etapa' => 'Inscritos', 
                    'valor' => $totalJogadores, 
                    'cor' => 'bg-blue-100 text-blue-800 border-blue-200'
                ],
                [
                    'etapa' => 'Avaliados (Compareceram)', 
                    'valor' => $avaliados, 
                    'cor' => 'bg-purple-100 text-purple-800 border-purple-200'
                ],
                [
                    'etapa' => 'Aprovados (Seleção Final)', 
                    'valor' => $totalAprovados, 
                    'cor' => 'bg-green-100 text-green-800 border-green-200'
                ],
            ],
            'jogadores_destaque' => $topDestaques->map(function($jogador) {
                return [
                    'id' => $jogador->id,
                    'nome_completo' => $jogador->pessoa->nome_completo,
                    'foto_perfil_url' => $jogador->pessoa->foto_perfil_url,
                    'rating_medio' => round($jogador->rating_medio ?? 0, 1),
                    'pessoa' => $jogador->pessoa
                ];
            })
        ];
    }
}