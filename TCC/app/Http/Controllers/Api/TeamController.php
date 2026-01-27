<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    // Gerar times automaticamente
    public function generate($peneiraId)
    {
        try {
            // Delega para o Service (que chama o GeradorEquipeService)
            $teams = $this->teamService->generateAuto($peneiraId);
            
            return response()->json([
                'message' => 'Times gerados com sucesso!',
                'data' => $teams
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Listar times existentes
    public function index($peneiraId)
    {
        // Usa o método de formatação que já existe no seu TeamService
        $teams = $this->teamService->getTeamsForPeneira($peneiraId);
        
        return response()->json($teams);
    }

    // Salvar alterações manuais (Drag & Drop)
    public function store(Request $request, $peneiraId)
    {
        $teamsData = $request->input('teams');

        try {
            $this->teamService->saveManualStructure($peneiraId, $teamsData);
            
            return response()->json(['success' => true, 'message' => 'Posições salvas!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar times: ' . $e->getMessage()], 500);
        }
    }
}