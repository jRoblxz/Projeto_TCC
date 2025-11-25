<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peneiras;
use App\Models\Equipe;
use App\Models\Jogadores;
use App\Services\GeradorEquipeService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EquipeController
{
    protected $geradorEquipeService;

    public function __construct(GeradorEquipeService $geradorEquipeService)
    {
        $this->geradorEquipeService = $geradorEquipeService;
    }

    /**
     * Gera equipes automaticamente usando o Service
     */
    public function montarEquipes(Request $request, $id)
    {
        $peneira = Peneiras::findOrFail($id);

        try {
            $this->geradorEquipeService->gerarEquipesParaPeneira($peneira);
            return redirect()->route('peneira.editar-equipes', $id)
                ->with('success', 'Equipes geradas com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Gera as equipes E redireciona para o editor visual
     */
    public function gerarERedirecionarParaEditor($id)
    {
        $peneira = Peneiras::findOrFail($id);

        try {
            // Verifica se já existem equipes para esta peneira
            $equipesExistentes = Equipe::where('peneira_id', $peneira->id)->count();

            if ($equipesExistentes > 0) {
                // Se já tem equipes, só redireciona para o editor
                return redirect()->route('equipes.tela-montagem', $id)
                    ->with('info', 'Equipes já foram geradas anteriormente. Você pode editá-las agora.');
            }

            // Se não tem equipes, gera automaticamente
            $this->geradorEquipeService->gerarEquipesParaPeneira($peneira);

            return redirect()->route('equipes.tela-montagem', $id)
                ->with('success', 'Equipes geradas com sucesso! Agora você pode editá-las.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Exibe a tela de edição visual das equipes
     */
    public function editarEquipes($id)
    {
        $peneira = Peneiras::findOrFail($id);

        $equipes = Equipe::with(['jogadores.pessoa'])
            ->where('peneira_id', $peneira->id)
            ->get();

        $teamsData = [];

        foreach ($equipes as $index => $equipe) {
            $teamLetter = chr(65 + $index);
            $teamsData[$teamLetter] = [];

            foreach ($equipe->jogadores as $jogador) {
                // [CORREÇÃO 1] Verificação de segurança para o nome
                $nomeJogador = 'Sem Nome';
                if ($jogador->pessoa) {
                    $nomeJogador = $jogador->pessoa->nome_completo ?? $jogador->pessoa->nome ?? 'Sem Nome';
                }

                $teamsData[$teamLetter][] = [
                    'id' => $jogador->id,
                    'name' => $nomeJogador, // [CORREÇÃO 1] Usando a variável segura
                    'pos' => $this->normalizarPosicao($jogador->posicao_principal),
                    'secondaryPos' => $jogador->posicao_secundaria
                        ? $this->normalizarPosicao($jogador->posicao_secundaria)
                        : '-',
                    // [CORREÇÃO 3] Usa o rating do banco, se não tiver, calcula
                    'rating' => $jogador->rating_medio ? $jogador->rating_medio : $this->calcularRating($jogador),
                    'inField' => true
                ];
            }
        }

        // Garante estrutura mínima A e B
        if (!isset($teamsData['A'])) $teamsData['A'] = [];
        if (!isset($teamsData['B'])) $teamsData['B'] = [];

        // Garante que só mandamos A e B para a view (para não quebrar o layout)
        $teamsData = array_intersect_key($teamsData, array_flip(['A', 'B']));

        $teamsJson = json_encode($teamsData);

        return view('peneira-detalhes', compact('peneira', 'teamsData', 'teamsJson'));
    }

    /**
     * Salva as alterações feitas no editor visual
     */
    public function salvarEquipes(Request $request, $id)
    {
        $peneira = Peneiras::findOrFail($id);
        $teams = $request->input('teams');

        try {
            DB::transaction(function () use ($peneira, $teams) {
                // Busca as equipes existentes
                $equipes = Equipe::where('peneira_id', $peneira->id)
                    ->orderBy('id')
                    ->get();

                $teamLetters = ['A', 'B'];

                foreach ($teamLetters as $index => $letter) {
                    if (!isset($teams[$letter])) continue;

                    $equipe = $equipes->get($index);

                    if (!$equipe) {
                        // Cria nova equipe se não existir
                        $equipe = Equipe::create([
                            'nome' => 'Equipe ' . $letter,
                            'peneira_id' => $peneira->id
                        ]);
                    }

                    // Atualiza os jogadores da equipe
                    $jogadoresIds = collect($teams[$letter])
                        ->pluck('id')
                        ->filter()
                        ->toArray();

                    $equipe->jogadores()->sync($jogadoresIds);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Times salvos com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normaliza as posições para o padrão FIFA
     */
    private function normalizarPosicao($posicao)
    {
        $mapa = [
            'Goleiro' => 'GK',
            'Zagueiro' => 'CB',
            'Zagueiro Direito' => 'CB',
            'Zagueiro Esquerdo' => 'CB',
            'Lateral' => 'LB',
            'Lateral Direito' => 'RB',
            'Lateral Esquerdo' => 'LB',
            'Volante' => 'CDM',
            'Meia' => 'CM',
            'Meia Atacante' => 'CAM',
            'Meia Direito' => 'RM',
            'Meia Esquerdo' => 'LM',
            'Atacante' => 'ST',
            'Ponta' => 'LW',
            'Ponta Direita' => 'RW',
            'Ponta Esquerda' => 'LW',
            'Centroavante' => 'ST',
        ];

        return $mapa[$posicao] ?? 'CM';
    }

    /**
     * Calcula um rating fictício baseado em atributos do jogador
     */
    private function calcularRating($jogador)
    {
        $base = 7.0;

        if (in_array($jogador->posicao_principal, ['Goleiro', 'Zagueiro'])) {
            if ($jogador->altura_cm >= 180) {
                $base += 0.5;
            }
        }

        // [CORREÇÃO 2] Segurança para Data de Nascimento
        if ($jogador->pessoa && $jogador->pessoa->data_nascimento) {
            try {
                // Tenta parsear a data caso ela seja string
                $dataNasc = Carbon::parse($jogador->pessoa->data_nascimento);
                $idade = $dataNasc->age;
                
                if ($idade < 20) {
                    $base += 0.3;
                }
            } catch (\Exception $e) {
                // Se der erro na data, ignora o bonus de idade
            }
        }

        $base += (mt_rand(0, 15) / 10);

        return min(9.9, $base); // Garante que não passe de 9.9
    }
}
