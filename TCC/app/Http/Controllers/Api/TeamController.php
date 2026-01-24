<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Services\GeradorEquipeService; // You keep this
use App\Models\Peneiras;
use App\Models\Equipe;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected $generatorService;

    public function __construct(GeradorEquipeService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    public function index($peneiraId)
    {
        $equipes = Equipe::with(['jogadores.pessoa'])
            ->where('peneira_id', $peneiraId)
            ->get();

        // Transform into the JSON structure your React Front-end needs
        // (The logic you had in 'editarEquipes' loop goes here)
        $teamsData = []; 
        // ... (Your loop logic from EquipeController::editarEquipes) ...

        return response()->json($teamsData);
    }

    public function generate($peneiraId)
    {
        $peneira = Peneiras::findOrFail($peneiraId);
        $this->generatorService->gerarEquipesParaPeneira($peneira);
        
        // Return fresh data so React updates immediately
        return $this->index($peneiraId);
    }

    public function store(Request $request, $peneiraId)
    {
        // The logic from 'salvarEquipes'
        // ... DB Transaction ...
        
        return response()->json(['success' => true, 'message' => 'Teams saved']);
    }
}