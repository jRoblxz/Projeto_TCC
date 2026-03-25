<?php

namespace App\Services;

use App\Models\Jogadores;
use App\Models\Peneiras;

class DashboardService
{
    public function getStats(array $filters)
    {
        // Pega o filtro se existir
        $subdivisao = $filters['subdivisao'] ?? null;

        // ==========================================
        // 1. CONSULTAS DE PENEIRAS (Rápido no SQL)
        // ==========================================
        $queryPeneiras = Peneiras::when($subdivisao, fn($q) => $q->where('sub_divisao', $subdivisao));

        // Usa o clone para não misturar os 'wheres' nas consultas seguintes
        $activeEventsCount = (clone $queryPeneiras)->where('status', 'EM_ANDAMENTO')->count();
        $agendadasEventsCount = (clone $queryPeneiras)->where('status', 'AGENDADA')->count();

        // Próximos eventos (Ordena no SQL e pega só 5)
        $nextEvents = (clone $queryPeneiras)
            ->whereIn('status', ['EM_ANDAMENTO', 'AGENDADA'])
            ->orderByDesc('data_evento')
            ->take(5)
            ->get();


        // ==========================================
        // 2. CONSULTAS DE JOGADORES (Corrigindo o Accessor)
        // ==========================================
        $queryJogadores = Jogadores::when($subdivisao, function ($q) use ($subdivisao) {
            // O whereHas filtra usando o banco de dados primeiro
            $q->whereHas('pessoa', fn($query) => $query->where('sub_divisao', $subdivisao));
        });

        // Conta direto no banco
        $TotalCandidates = (clone $queryJogadores)->count();

        // Como 'rating_medio' é calculado no PHP, nós trazemos a lista filtrada pra memória e ordenamos!
        $jogadoresFiltrados = (clone $queryJogadores)
            ->with('pessoa')
            ->get() // 1. Traz os jogadores do banco (apenas os que passaram no filtro)
            ->sortByDesc('rating_medio') // 2. Ordena no PHP usando a sua função
            ->take(10) // 3. Pega só os 10 primeiros
            ->values(); // 4. Reorganiza os índices para o React não reclamar   


        // ==========================================
        // 3. RETORNO
        // ==========================================
        return [
            'stats' => [
                'total_candidatos'   => $TotalCandidates,
                'peneiras_ativas'    => $activeEventsCount,
                'peneiras_agendadas' => $agendadasEventsCount,
                'total_peneiras'     => $activeEventsCount + $agendadasEventsCount
            ],
            'recent_events' => $nextEvents,
            'jogadores'     => $jogadoresFiltrados
        ];
    }
}