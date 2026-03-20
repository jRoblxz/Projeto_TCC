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
        $nextEvents = $queryPeneiras->whereIn('status', ['EM_ANDAMENTO', 'AGENDADA'])->orderByDesc('data_evento')->take(5)->get();

        $TotalCandidates = Jogadores::with('pessoa')->get()
            ->when(!empty($filters['subdivisao']), function ($collection) use ($filters) {
                return $collection->filter(function ($jogador) use ($filters) {
                    return $jogador->pessoa && ($jogador->pessoa->sub_divisao == $filters['subdivisao']);
                });
            })
            ->count();

        $activeEventsCount = Peneiras::whereIn('status', ['EM_ANDAMENTO'])
            ->when(!empty($filters['subdivisao']), fn($q) => $q->where('sub_divisao', $filters['subdivisao']))
            ->count();

        $agendadasEventsCount = Peneiras::whereIn('status', ['AGENDADA'])
            ->when(!empty($filters['subdivisao']), fn($q) => $q->where('sub_divisao', $filters['subdivisao']))
            ->count();

        // 4. Retorno Formatado
        return [
            'stats' => [
                'total_candidatos' => $TotalCandidates,
                'peneiras_ativas'    => $activeEventsCount,
                'peneiras_agendadas'  => $agendadasEventsCount,
                'total_peneiras' => $activeEventsCount + $agendadasEventsCount
            ],
            'recent_events' => $nextEvents,
            'jogadores'     => $jogadoresFiltrados
        ];
    }
}
