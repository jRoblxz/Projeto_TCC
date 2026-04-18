<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeneiraResource;
use App\Models\Peneiras;
use App\Services\PeneiraService;
use Illuminate\Http\Request;

class PeneiraController extends Controller
{
    protected $peneiraService;

    public function __construct(PeneiraService $peneiraService)
    {
        $this->peneiraService = $peneiraService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'sub_divisao' => $request->input('sub_divisao'),
            'status' => $request->input('status') 
        ];

        $perPage = $request->input('per_page', 9);

        $peneiras = $this->peneiraService->getAll($perPage, $filters);

        return PeneiraResource::collection($peneiras);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_evento' => 'required|string|max:255',
            'data_evento' => 'required|date',
            'local' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'sub_divisao' => 'nullable|string',
            'descricao' => 'nullable|string',
        ]);

        $peneira = $this->peneiraService->create($data);
        return new PeneiraResource($peneira);
    }

    public function show($id)
    {
        // Carrega a peneira E intercepta a chamada do jogador para injetar a média específica
        $peneira = Peneiras::with([
            'inscricoes.jogador' => function ($query) use ($id) {
                $query->with('pessoa')
                      // O SEGREDO AQUI: Calcula a média APENAS com as notas desta peneira ($id)
                      ->withAvg(['avaliacoes as rating_medio' => function($q) use ($id) {
                          $q->where('peneira_id', $id);
                      }], 'nota');
            }
        ])->findOrFail($id);

        return response()->json([
            'peneira' => $peneira,
            
            // Mapeia para pegar os jogadores e garantir que o rating vai formatado
            'jogadores' => $peneira->inscricoes->map(function ($inscricao) {
                $jogador = $inscricao->jogador;
                
                // Se a base de dados não encontrar notas para ESTA peneira, 
                // o rating_medio vem nulo. Transformamos em 0.0 para o React ler corretamente.
                $jogador->rating_medio = (float) ($jogador->rating_medio ?? 0.0);
                
                // Opcional, mas muito útil: enviamos também o status da inscrição para o Frontend
                $jogador->status_inscricao = $inscricao->status;
                
                return $jogador;
            })
        ]);
    }

    public function update(Request $request, $id)
    {
        $peneira = $this->peneiraService->update($id, $request->all());
        return new PeneiraResource($peneira);
    }

    public function destroy($id)
    {
        try {
            $this->peneiraService->delete($id);
            return response()->json(['message' => 'Peneira deletada com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar: ' . $e->getMessage()], 400);
        }
    }
}