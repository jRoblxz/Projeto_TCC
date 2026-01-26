<?php

namespace App\Services;

use App\Models\Jogadores;
use App\Models\Peneiras;
use App\Models\User;

class DashboardService
{
    public function getStats(array $filters)
    {
        // 1. Query Base de Peneiras
        $queryPeneiras = Peneiras::query();

        // 2. Buscar e Filtrar Jogadores (Lógica movida da Controller)
        $allJogadores = Jogadores::with('pessoa')->get()
            ->sortByDesc('rating_medio')
            ->values();

        if (!empty($filters['subdivisao'])) {
            $sub = $filters['subdivisao'];
            
            // Filtra Peneiras
            $queryPeneiras->where('sub_divisao', $sub);

            // Filtra Jogadores
            $jogadoresFiltrados = $allJogadores->filter(function ($jogador) use ($sub) {
                return $jogador->pessoa && ($jogador->pessoa->sub_divisao == $sub);
            })->values();
        } else {
            // Sem filtro: Top 10
            $jogadoresFiltrados = $allJogadores->take(10);
        }

        // 3. Consultas Auxiliares
        $nextEvents = $queryPeneiras->orderByDesc('data_evento')->take(5)->get();
        
        $activeEventsCount = Peneiras::whereIn('status', ['EM_ANDAMENTO', 'AGENDADA'])
            ->when(!empty($filters['subdivisao']), fn($q) => $q->where('sub_divisao', $filters['subdivisao']))
            ->count();

        // 4. Retorno Formatado
        return [
            'stats' => [
                'total_candidates' => $jogadoresFiltrados->count(),
                'active_events'    => $activeEventsCount,
                'evaluators'       => User::where('role', 'avaliador')->count(),
                'aprovados'        => 0,
                'em_avaliacao'     => 0,
            ],
            'recent_events' => $nextEvents,
            'jogadores'     => $jogadoresFiltrados
        ];
    }
}