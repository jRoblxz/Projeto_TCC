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
        // 2. CONSULTAS DE JOGADORES (Filtro de Idade Corrigido!)
        // ==========================================
        $queryJogadores = Jogadores::when($subdivisao, function ($q) use ($subdivisao) {
            
            // Converte a subdivisão nas idades correspondentes para filtrar pela data de nascimento
            $idades = match ($subdivisao) {
                'Sub-7'  => [6, 7],
                'Sub-9'  => [8, 9],
                'Sub-11' => [10, 11],
                'Sub-13' => [12, 13],
                'Sub-15' => [14, 15],
                'Sub-17' => [16, 17],
                'Sub-20' => [18, 21], // Baseado na sua model Pessoas.php
                default  => null
            };

            if ($idades) {
                // Transforma a idade numa janela de datas (Ano de nascimento)
                $dataInicio = now()->subYears($idades[1] + 1)->format('Y-m-d');
                $dataFim    = now()->subYears($idades[0])->format('Y-m-d');
                
                // Filtra usando a coluna real do banco de dados (data_nascimento)
                $q->whereHas('pessoa', function($query) use ($dataInicio, $dataFim) {
                    $query->whereBetween('data_nascimento', [$dataInicio, $dataFim]);
                });
            }
        });

        // Conta direto no banco
        $TotalCandidates = (clone $queryJogadores)->count();

        // O SEGREDO ESTÁ AQUI: withAvg()
        // Ele vai na tabela 'avaliacoes', tira a média da coluna 'nota' e finge que o 
        // nome dessa coluna é 'rating_medio'. Isso mata o N+1 instantaneamente!
        $jogadoresFiltrados = (clone $queryJogadores)
            ->with('pessoa')
            ->withAvg('avaliacoes as rating_medio', 'nota') // A Mágica do Laravel
            ->orderByDesc('rating_medio') // Como a coluna agora existe, ordenamos no SQL!
            ->take(10) // Trazemos APENAS 10 jogadores para a RAM
            ->get();   


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