<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PlayerService;
use App\Models\Peneiras;
use App\Models\Inscricoes;

class DashboardController extends Controller
{
    protected $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    public function index(Request $request)
    {
        // 1. Pega o filtro que vem do React LOGO NO COMEÇO
        $filtroSub = $request->query('subdivisao');

        // 2. Passa o filtro para o Service (Isso arruma o Funil e os Destaques!)
        $playerStats = $this->playerService->getStats($filtroSub);

        // 3. Calcula as métricas dos 4 Cards do topo COM FILTRO
        $pQueryAtivas = Peneiras::whereIn('status', ['EM_ANDAMENTO', 'Em Andamento', 'em_andamento']);
        $pQueryAgendadas = Peneiras::whereIn('status', ['AGENDADA', 'Agendada', 'agendada']);
        $pQueryTotal = Peneiras::query();

        if ($filtroSub) {
            $pQueryAtivas->where('sub_divisao', $filtroSub);
            $pQueryAgendadas->where('sub_divisao', $filtroSub);
            $pQueryTotal->where('sub_divisao', $filtroSub);
        }

        // 4. Monta o objeto 'stats' com os totais já filtrados
        $stats = [
            'total_candidatos' => $playerStats['total'],
            'peneiras_ativas' => $pQueryAtivas->count(),
            'peneiras_agendadas' => $pQueryAgendadas->count(),
            'total_peneiras' => $pQueryTotal->count(),
            'funil_conversao' => $playerStats['funil_conversao']
        ];

        // 5. Busca as peneiras da lista COM FILTRO
        $peneirasQuery = Peneiras::whereIn('status', ['AGENDADA', 'EM_ANDAMENTO']);
        
        if ($filtroSub) {
            $peneirasQuery->where('sub_divisao', $filtroSub);
        }

        $peneiras = $peneirasQuery->orderBy('data_evento', 'asc') 
            ->take(5)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'nome_evento' => $p->nome_evento,
                    'data_evento' => $p->data_evento, 
                    'local' => $p->local,
                    'status' => strtoupper($p->status),
                    'sub_divisao' => $p->sub_divisao,
                    'inscricoes_count' => Inscricoes::where('peneira_id', $p->id)->count()
                ];
            });

        // 6. Os jogadores já vêm filtrados automaticamente do getStats() !
        $jogadoresDestaque = $playerStats['jogadores_destaque'];

        return response()->json([
            'stats' => $stats,
            'peneiras' => $peneiras,
            'jogadores' => $jogadoresDestaque
        ]);
    }
}