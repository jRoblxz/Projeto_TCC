<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jogadores;
use App\Models\Peneiras;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Preparar Query de Peneiras
        $queryPeneiras = Peneiras::query();

        // 2. Buscar Jogadores
        // [CORREÇÃO]: Não usamos 'orderBy' aqui pois rating_medio não existe no banco.
        // Trazemos todos e ordenamos na memória logo abaixo.
        $allJogadores = Jogadores::with('pessoa')->get(); 
        
        // [CORREÇÃO]: Ordenação via PHP (Coleção)
        // Isso usa o seu "getRatingMedioAttribute" para ordenar do maior para o menor
        $allJogadores = $allJogadores->sortByDesc('rating_medio')->values();

        // --- LÓGICA DE FILTRO ---
        if ($request->filled('subdivisao')) {
            $filtro = $request->subdivisao; // Ex: "Sub-13"

            // A. Filtra Peneiras (SQL)
            $queryPeneiras->where('sub_divisao', $filtro);

            // B. Filtra Jogadores (PHP - Collection)
            $jogadoresFiltrados = $allJogadores->filter(function ($jogador) use ($filtro) {
                return $jogador->pessoa && ($jogador->pessoa->sub_divisao == $filtro);
            })->values(); 
        } else {
            // Sem filtro? Pega apenas os top 10 (já ordenados pelo rating)
            $jogadoresFiltrados = $allJogadores->take(10);
        }

        // 3. Obter Resultados Finais
        
        // Peneiras Recentes
        $nextEvents = $queryPeneiras->orderByDesc('data_evento')
                        ->take(5)
                        ->get();

        // Peneiras Ativas (Contagem)
        $activeEventsCount = Peneiras::whereIn('status', ['EM_ANDAMENTO', 'AGENDADA'])
                            ->when($request->filled('subdivisao'), function($q) use ($request) {
                                $q->where('sub_divisao', $request->subdivisao);
                            })
                            ->count();

        // 4. Montar Resposta JSON
        return response()->json([
            'stats' => [
                'total_candidates' => $jogadoresFiltrados->count(),
                'active_events' => $activeEventsCount,
                'evaluators' => User::where('role', 'avaliador')->count(),
                'aprovados' => 0, 
                'em_avaliacao' => 0,
            ],
            'recent_events' => $nextEvents,
            'jogadores' => $jogadoresFiltrados // Agora enviamos a lista ordenada por rating
        ]);
    }
}