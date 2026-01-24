<?php

namespace App\Services;

use App\Models\Peneiras;
use App\Models\Equipe;
use Illuminate\Support\Facades\DB;
use App\Services\GeradorEquipeService; // Mantém seu gerador original para a lógica matemática

class TeamService
{
    protected $geradorAlgoritmo;

    public function __construct(GeradorEquipeService $gerador)
    {
        $this->geradorAlgoritmo = $gerador;
    }

    // Retorna os dados formatados para o Frontend (Drag and Drop)
    public function getTeamsForPeneira($peneiraId)
    {
        $equipes = Equipe::with(['jogadores.pessoa'])
            ->where('peneira_id', $peneiraId)
            ->get();

        $teamsData = [];
        
        // Lógica de formatação que estava no Controller
        foreach ($equipes as $index => $equipe) {
            $letter = chr(65 + $index); // A, B, C...
            $teamsData[$letter] = $equipe->jogadores->map(function($jogador) {
                return [
                    'id' => $jogador->id,
                    'name' => $jogador->pessoa->nome_completo ?? 'Sem Nome',
                    'pos' => $jogador->posicao_principal, // Idealmente normalizar aqui
                    'rating' => $jogador->rating_medio ?? 7.0,
                ];
            });
        }
        
        return $teamsData;
    }

    // Gera automaticamente usando seu serviço existente
    public function generateAuto($peneiraId)
    {
        $peneira = Peneiras::findOrFail($peneiraId);
        
        // Limpa equipes anteriores se necessário ou verifica se já existem
        // Chama seu serviço de algoritmo
        $this->geradorAlgoritmo->gerarEquipesParaPeneira($peneira);
        
        return $this->getTeamsForPeneira($peneiraId);
    }

    // Salva a edição manual feita no Frontend
    public function saveManualStructure($peneiraId, array $teamsData)
    {
        return DB::transaction(function () use ($peneiraId, $teamsData) {
            $peneira = Peneiras::findOrFail($peneiraId);
            $equipesDB = Equipe::where('peneira_id', $peneiraId)->orderBy('id')->get();

            foreach ($teamsData as $teamLetter => $players) {
                // Mapeia A -> index 0, B -> index 1
                $index = ord($teamLetter) - 65;
                
                // Pega a equipe ou cria se não existir
                $equipe = $equipesDB->get($index);
                if (!$equipe) {
                    $equipe = Equipe::create([
                        'nome' => 'Equipe ' . $teamLetter,
                        'peneira_id' => $peneiraId
                    ]);
                }

                // Sincroniza os IDs dos jogadores nesta equipe
                $ids = collect($players)->pluck('id')->toArray();
                $equipe->jogadores()->sync($ids);
            }
            return true;
        });
    }
}