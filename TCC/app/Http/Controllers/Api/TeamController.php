<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peneiras;
use App\Models\Equipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection; // Importante para checagem

class TeamController extends Controller
{
    public function generate($peneiraId)
    {
        return DB::transaction(function () use ($peneiraId) {
            $peneira = Peneiras::with(['inscricoes.jogador.avaliacoes'])->findOrFail($peneiraId);
            
            // 1. Limpar antigos
            $equipesAntigas = Equipe::where('peneira_id', $peneiraId)->get();
            foreach($equipesAntigas as $eq) {
                DB::table('JogadoresPorEquipe')->where('equipe_id', $eq->id)->delete();
                $eq->delete();
            }

            // 2. Criar Times
            $timeA = Equipe::create(['nome' => 'Time A', 'peneira_id' => $peneiraId]);
            $timeB = Equipe::create(['nome' => 'Time B', 'peneira_id' => $peneiraId]);

            // 3. Ordenar (Lógica Corrigida)
            $jogadores = $peneira->inscricoes
                ->map(function($inscricao) {
                    $jogador = $inscricao->jogador;
                    $rating = 0;

                    // Verifica se avaliacoes é uma coleção e se não está vazia
                    if ($jogador && $jogador->relationLoaded('avaliacoes')) {
                        $avals = $jogador->avaliacoes;
                        if ($avals instanceof Collection && $avals->isNotEmpty()) {
                            $rating = $avals->avg('nota');
                        }
                    }
                    
                    if ($jogador) {
                        $jogador->rating_temp = $rating ?? 0;
                    }
                    return $jogador;
                })
                ->filter()
                ->sortByDesc('rating_temp')
                ->values();

            // 4. Distribuir
            foreach ($jogadores as $index => $jogador) {
                if (!$jogador) continue;
                $targetTeam = ($index % 2 === 0) ? $timeA : $timeB;
                $isTitular = floor($index / 2) < 11; 

                DB::table('JogadoresPorEquipe')->insert([
                    'equipe_id' => $targetTeam->id,
                    'jogador_id' => $jogador->id,
                    'titular' => $isTitular,
                    'posicao_campo_x' => 50,
                    'posicao_campo_y' => 50
                ]);
            }

            return response()->json(['message' => 'Times gerados com sucesso!']);
        });
    }

    public function index($peneiraId)
    {
        $equipes = Equipe::where('peneira_id', $peneiraId)->get();
        $result = [];
        
        foreach ($equipes as $equipe) {
            $jogadores = $equipe->jogadores()
                ->with(['pessoa', 'avaliacoes'])
                ->get()
                ->map(function ($jogador) {
                    
                    // Cálculo Seguro
                    $rating = 0;
                    $avals = $jogador->avaliacoes;
                    
                    // Se for Collection, usa avg(). Se não, tenta pegar 'nota' direto se for objeto.
                    if ($avals instanceof Collection) {
                        if ($avals->isNotEmpty()) {
                            $rating = $avals->avg('nota');
                        }
                    } elseif ($avals) {
                        // Caso seja hasOne (retorna objeto)
                        $rating = $avals->nota ?? 0;
                    }

                    return [
                        'id' => $jogador->id,
                        'name' => $jogador->pessoa->nome_completo ?? 'Sem Nome',
                        'rating' => round($rating, 1),
                        'pos' => $jogador->posicao_principal,
                        'secondaryPos' => $jogador->posicao_secundaria,
                        
                        // Pivot Data
                        'x' => $jogador->pivot->posicao_campo_x ?? 50,
                        'y' => $jogador->pivot->posicao_campo_y ?? 50,
                        'inField' => (bool) $jogador->pivot->titular
                    ];
                });

            $key = str_contains($equipe->nome, 'A') ? 'A' : 'B';
            $result[$key] = [
                'id' => $equipe->id,
                'nome' => $equipe->nome,
                'formation' => '4-4-2',
                'players' => $jogadores
            ];
        }

        return response()->json($result);
    }

   // [CORREÇÃO] O nome deve ser 'store' para bater com a sua rota
    public function store(Request $request, $peneiraId)
    {
        $teamsData = $request->input('teams'); 

        DB::transaction(function () use ($teamsData) {
            foreach ($teamsData as $teamKey => $teamData) {
                if (!isset($teamData['players'])) continue;
                $equipeId = $teamData['id'];
                
                foreach ($teamData['players'] as $player) {
                    DB::table('JogadoresPorEquipe')
                        ->where('equipe_id', $equipeId)
                        ->where('jogador_id', $player['id'])
                        ->update([
                            'posicao_campo_x' => $player['x'],
                            'posicao_campo_y' => $player['y'],
                            'titular' => $player['inField'] ? 1 : 0
                        ]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Posições salvas!']);
    }
}