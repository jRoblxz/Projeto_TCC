<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CandidateService;
use App\Services\PeneiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicController extends Controller
{
    protected $candidateService;
    protected $peneiraService;

    // Injeção de Dependência
    public function __construct(CandidateService $candidateService, PeneiraService $peneiraService)
    {
        $this->candidateService = $candidateService;
        $this->peneiraService = $peneiraService;
    }

    public function getOpenPeneiras()
    {
        // Usa o service existente (ou cria um método específico lá)
        $peneiras = \App\Models\Peneiras::whereIn('status', ['AGENDADA', 'EM_ANDAMENTO'])
            ->select('id', 'title', 'date', 'subdivision', 'status')
            ->orderByDesc('date')
            ->get();
            
        return response()->json($peneiras);
    }

    public function registerCandidate(Request $request)
    {
        $validated = $request->validate([
            'nome_completo' => 'required|string',
            'email' => 'required|email|unique:pessoas,email',
            'cpf' => 'required|string|unique:pessoas,cpf',
            'peneira_id' => 'required|exists:peneiras,id',
            'data_nascimento' => 'required|date',
            'posicao_principal' => 'required|string',
            'foto_perfil_url' => 'nullable|file|image|max:5120',
            // ... valide os outros campos opcionais aqui se necessário
        ]);

        try {
            // A Controller apenas delega para o Service
            $this->candidateService->register(
                $request->all(), 
                $request->file('foto_perfil_url')
            );

            return response()->json(['message' => 'Inscrição realizada com sucesso!'], 201);

        } catch (\Exception $e) {
            Log::error("Erro no registro: " . $e->getMessage());
            // Em produção, evite enviar $e->getMessage() direto para o user, use msg genérica
            return response()->json(['error' => 'Erro interno ao processar inscrição: ' . $e->getMessage()], 500);
        }
    }
}