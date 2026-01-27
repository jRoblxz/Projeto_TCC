<?php

namespace App\Services;

use App\Models\Peneiras;
use App\Models\Equipe;
use Illuminate\Support\Facades\DB;
use App\Services\GeradorEquipeService;

class TeamService
{
    protected $geradorAlgoritmo;

    public function __construct(GeradorEquipeService $gerador)
    {
        $this->geradorAlgoritmo = $gerador;
    }

    // Retorna os dados formatados para o Frontend
    public function getTeamsForPeneira($peneiraId)
    {
        $equipes = Equipe::with(['jogadores.pessoa'])
            ->where('peneira_id', $peneiraId)
            ->orderBy('nome') // Garante ordem (Time A, Time B)
            ->get();

        $teamsList = []; // Mudamos o nome para indicar que é uma lista
        
        foreach ($equipes as $equipe) {
            // Formata a lista de jogadores deste time
            $playersFormatted = $equipe->jogadores->map(function($jogador) {
                return [
                    'id' => $jogador->id,
                    'name' => $jogador->pessoa->nome_completo ?? 'Sem Nome',
                    'pos' => $jogador->posicao_principal, 
                    // Garante que o rating seja um número (float)
                    'rating' => (float) ($jogador->rating_medio ?? 7.0),
                    // Mantém dados de posição se existirem na pivot
                    'x' => $jogador->pivot->posicao_campo_x ?? 0,
                    'y' => $jogador->pivot->posicao_campo_y ?? 0,
                    'inField' => (bool) ($jogador->pivot->titular ?? false)
                ];
            });

            // Adiciona ao array principal como um objeto estruturado
            $teamsList[] = [
                'id' => $equipe->id,
                'name' => $equipe->nome, // Ex: "Equipe A"
                'players' => $playersFormatted
            ];
        }
        
        // Retorna a lista simples que o React consegue ler
        return $teamsList;
    }

    // ... Mantenha os métodos generateAuto e saveManualStructure iguais ...
    public function generateAuto($peneiraId)
    {
        $peneira = Peneiras::findOrFail($peneiraId);
        $this->geradorAlgoritmo->gerarEquipesParaPeneira($peneira);
        return $this->getTeamsForPeneira($peneiraId);
    }

    public function saveManualStructure($peneiraId, array $teamsData)
    {
        return DB::transaction(function () use ($peneiraId, $teamsData) {
            $peneira = Peneiras::findOrFail($peneiraId);
            
            // Busca as equipes existentes ordenadas para mapear A e B corretamente
            $equipesDB = Equipe::where('peneira_id', $peneiraId)->orderBy('id')->get();

            foreach ($teamsData as $teamLetter => $teamData) {
                // O frontend envia um objeto completo { id: 1, players: [...] }
                // Precisamos acessar a chave 'players' se ela existir, ou usar o próprio array se for direto
                $playersList = $teamData['players'] ?? $teamData;

                if (!is_array($playersList)) continue;

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

                // [CORREÇÃO CRÍTICA] Prepara os dados para o sync COM AS POSIÇÕES
                $syncData = [];
                foreach ($playersList as $player) {
                    $playerId = $player['id'];
                    $syncData[$playerId] = [
                        'posicao_campo_x' => $player['x'] ?? 50,
                        'posicao_campo_y' => $player['y'] ?? 50,
                        'titular'         => isset($player['inField']) && $player['inField'] ? 1 : 0
                    ];
                }

                // Salva mantendo as posições
                $equipe->jogadores()->sync($syncData);
            }
            return true;
        });
    }
}