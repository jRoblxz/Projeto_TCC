<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeneiraResource;
use App\Models\Peneiras;
use App\Services\PeneiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'status' => $request->input('status') // <--- ADICIONE ESTA LINHA
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
        // Carrega a peneira E os jogadores inscritos (com os dados da pessoa)
        $peneira = Peneiras::with(['inscricoes.jogador.pessoa', 'inscricoes.jogador.ultima_avaliacao'])->findOrFail($id);

        // Formata os dados para facilitar no frontend se necessário
        // Ou retorna direto se a estrutura do JSON já for amigável

        return response()->json([
            'peneira' => $peneira,
            // Mapeia para pegar os jogadores de dentro das inscrições
            'jogadores' => $peneira->inscricoes->map(function ($inscricao) {
                return $inscricao->jogador;
            })
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validação similar ao store...
        $peneira = $this->peneiraService->update($id, $request->all());
        return new PeneiraResource($peneira);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // 1. LIMPEZA DE EQUIPES (A parte mais complexa)
            // Primeiro, pegamos os IDs dos times dessa peneira
            $equipeIds = DB::table('equipes')->where('peneira_id', $id)->pluck('id');

            if ($equipeIds->count() > 0) {
                // Remove os jogadores vinculados a esses times (Tabela 'jogadoresporequipe')
                // Usamos whereIn para apagar de todos os times de uma vez
                DB::table('jogadoresporequipe')->whereIn('equipe_id', $equipeIds)->delete();

                // Agora que os times estão vazios, podemos apagá-los
                DB::table('equipes')->where('peneira_id', $id)->delete();
            }

            // 2. Apagar Avaliações
            DB::table('avaliacoes')->where('peneira_id', $id)->delete();

            // 3. Apagar Inscrições
            DB::table('inscricoes')->where('peneira_id', $id)->delete();

            // 4. Finalmente, apagar a Peneira
            $peneira = \App\Models\Peneiras::findOrFail($id);
            $peneira->delete();

            DB::commit();

            return response()->json(['message' => 'Peneira e todos os dados vinculados foram deletados com sucesso.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao deletar: ' . $e->getMessage()], 400);
        }
    }
}
