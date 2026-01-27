<?php

namespace App\Services;

use App\Models\Peneiras;
use App\Models\Equipe;
use App\Models\Jogadores;
use App\Models\Inscricoes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeradorEquipeService
{
    // Define o tamanho da equipe
    const TAMANHO_EQUIPE = 11;

    // Define a formação tática (quantos de cada posição)
    const FORMACAO_SLOTS = [
        'Goleiro'  => 1,
        'Zagueiro' => 2,
        'Lateral'  => 2,
        'Volante'  => 1,
        'Meia'     => 3, // 1 Volante + 3 Meias
        'Atacante' => 2,
    ]; // Total = 11

    /**
     * Método principal que gera as equipes para uma peneira.
     */
    public function gerarEquipesParaPeneira(Peneiras $peneira)
    {
        // 1. Busca IDs de jogadores inscritos na peneira
        $jogadoresInscritosIds = Inscricoes::where('peneira_id', $peneira->id)
            ->pluck('jogador_id');

        // 2. Busca IDs de jogadores que JÁ estão em alguma equipe DESTA peneira
        $jogadoresEmEquipeIds = DB::table('Equipes AS e')
            ->join('JogadoresPorEquipe AS jpe', 'e.id', '=', 'jpe.equipe_id')
            ->where('e.peneira_id', $peneira->id)
            ->pluck('jpe.jogador_id');

        // 3. Busca os Modelos dos jogadores que estão inscritos E sem equipe
        $jogadoresDisponiveis = Jogadores::with('pessoa')
            ->whereIn('id', $jogadoresInscritosIds)
            ->whereNotIn('id', $jogadoresEmEquipeIds)
            ->get()
            ->shuffle();

        // 4. Verifica se tem gente suficiente
        if ($jogadoresDisponiveis->count() < self::TAMANHO_EQUIPE) {
            throw new \Exception('Não há jogadores suficientes (' . $jogadoresDisponiveis->count() . ') para formar uma nova equipe de 11.');
        }

        // 5. Tenta montar equipes enquanto houver jogadores
        $numeroEquipe = 1;
        while ($jogadoresDisponiveis->count() >= self::TAMANHO_EQUIPE) {

            $equipeSlots = self::FORMACAO_SLOTS; // Reseta as vagas para esta equipe
            $jogadoresSelecionados = collect(); // Coleção vazia para a nova equipe

            // Nível 1: Preencher por Posição Principal
            $this->preencherSlotsPorPosicao($jogadoresDisponiveis, $jogadoresSelecionados, $equipeSlots, 'posicao_principal');

            // Nível 2: Preencher por Posição Secundária
            $this->preencherSlotsPorPosicao($jogadoresDisponiveis, $jogadoresSelecionados, $equipeSlots, 'posicao_secundaria');

            // Nível 3: Preencher vagas restantes com Regras Físicas
            $this->preencherSlotsPorFisico($jogadoresDisponiveis, $jogadoresSelecionados, $equipeSlots);

            // 6. Salva a equipe no banco de dados se ela estiver completa
            if ($jogadoresSelecionados->count() == self::TAMANHO_EQUIPE) {

                // Usa uma transação para garantir que tudo funcione
                DB::transaction(function () use ($peneira, $jogadoresSelecionados, &$jogadoresDisponiveis, $numeroEquipe) {

                    // Cria a nova equipe com o campo teams_equipe
                    $equipe = Equipe::create([
                        'nome_equipe' => 'Equipe ' . chr(64 + $numeroEquipe), // Equipe A, B, C...
                        'peneira_id' => $peneira->id,
                    ]);

                    // Anexa os 11 jogadores na tabela 'JogadoresPorEquipe'
                    $equipe->jogadores()->attach($jogadoresSelecionados->pluck('id'));
                });

                // Remove os jogadores selecionados da lista de disponíveis
                $jogadoresDisponiveis = $jogadoresDisponiveis->diff($jogadoresSelecionados);
                $numeroEquipe++;
            } else {
                // Se não conseguiu 11, para o loop (não há mais como montar)
                break;
            }
        }
    }

    /**
     * Normaliza as posições (ex: 'Zagueiro Direito' vira 'Zagueiro')
     */
    private function getPosicaoNormalizada($posicao)
    {
        if (Str::contains($posicao, 'Goleiro')) return 'Goleiro';
        if (Str::contains($posicao, 'Zagueiro')) return 'Zagueiro';
        if (Str::contains($posicao, 'Lateral')) return 'Lateral';
        if (Str::contains($posicao, 'Volante')) return 'Volante';
        if (Str::contains($posicao, 'Meia')) return 'Meia';
        if (Str::contains($posicao, ['Atacante', 'Ponta', 'Centroavante'])) return 'Atacante';
        return 'Indefinido';
    }

    /**
     * Lógica Nível 1 e 2: Preenche vagas por posição (principal ou secundária)
     */
    private function preencherSlotsPorPosicao(&$jogadoresDisponiveis, &$jogadoresSelecionados, &$equipeSlots, $campoPosicao)
    {
        foreach ($equipeSlots as $posicao => $vagasRestantes) {
            if ($vagasRestantes == 0) continue;

            $candidatos = $jogadoresDisponiveis->filter(function ($jogador) use ($posicao, $campoPosicao) {
                return $this->getPosicaoNormalizada($jogador->{$campoPosicao}) === $posicao;
            });

            $selecionados = $candidatos->take($vagasRestantes);

            foreach ($selecionados as $jogador) {
                $jogadoresSelecionados->push($jogador);
                $equipeSlots[$posicao]--;
                $jogadoresDisponiveis = $jogadoresDisponiveis->except($jogador->id);
            }
        }
    }

    /**
     * Lógica Nível 3: Preenche vagas restantes com regras físicas
     */
    private function preencherSlotsPorFisico(&$jogadoresDisponiveis, &$jogadoresSelecionados, &$equipeSlots)
    {
        foreach ($equipeSlots as $posicao => $vagasRestantes) {
            if ($vagasRestantes == 0) continue;

            $candidatos = collect();


            switch ($posicao) {
                case 'Goleiro':
                    // REGRA: Goleiro deve ter no mínimo 1.80m
                    $candidatos = $jogadoresDisponiveis->where('altura_cm', '>=', 180)
                        ->sortByDesc('altura_cm'); // Pega o mais alto
                    break;

                case 'Zagueiro':
                    // REGRA: Zagueiro deve ter no mínimo 1.78m
                    $candidatos = $jogadoresDisponiveis->where('altura_cm', '>=', 178)
                        ->sortByDesc('altura_cm');
                    break;

                case 'Atacante':
                case 'Lateral':
                    // REGRA: Jogadores mais baixos (ex: < 1.75m)
                    // E mais novos (ex: < 20 anos)
                    $candidatos = $jogadoresDisponiveis->filter(function ($jogador) {
                        $idade = $jogador->pessoa && $jogador->pessoa->data_nascimento
                            ? $jogador->pessoa->data_nascimento->age
                            : 25;
                        return $jogador->altura_cm <= 175 && $idade < 20;
                    })->sortByDesc('pessoa.data_nascimento.age'); // Pega o mais novo
                    break;

                default: // Volante, Meia
                    $candidatos = $jogadoresDisponiveis; // Pega quem sobrou
            }

            $selecionados = $candidatos->take($vagasRestantes);

            foreach ($selecionados as $jogador) {
                $jogadoresSelecionados->push($jogador);
                $equipeSlots[$posicao]--;
                $jogadoresDisponiveis = $jogadoresDisponiveis->except($jogador->id);
            }
        }

        // Etapa Final: Se AINDA faltar gente, preenche com quem sobrou só para bater 11
        $vagasFaltando = self::TAMANHO_EQUIPE - $jogadoresSelecionados->count();
        if ($vagasFaltando > 0) {
            $quemSobrou = $jogadoresDisponiveis->take($vagasFaltando);
            foreach ($quemSobrou as $jogador) {
                $jogadoresSelecionados->push($jogador);
                $jogadoresDisponiveis = $jogadoresDisponiveis->except($jogador->id);
            }
        }
    }
}
