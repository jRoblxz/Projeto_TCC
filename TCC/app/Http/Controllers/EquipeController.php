<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peneiras;
use App\Models\Equipe;
use App\Models\Jogadores;
use App\Services\GeradorEquipeService;
use Illuminate\Support\Facades\DB;

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

        // Busca as equipes criadas para esta peneira
        $equipes = Equipe::with(['jogadores.pessoa'])
            ->where('peneira_id', $peneira->id)
            ->get();

        // Formata os dados para o formato esperado pela view
        $teamsData = [];

        foreach ($equipes as $index => $equipe) {
            $teamLetter = chr(65 + $index); // A, B, C, D...
            $teamsData[$teamLetter] = [];

            foreach ($equipe->jogadores as $jogador) {
                $teamsData[$teamLetter][] = [
                    'id' => $jogador->id,
                    'name' => $jogador->pessoa->nome ?? 'Sem Nome',
                    'pos' => $this->normalizarPosicao($jogador->posicao_principal),
                    'secondaryPos' => $jogador->posicao_secundaria
                        ? $this->normalizarPosicao($jogador->posicao_secundaria)
                        : '-',
                    'rating' => $this->calcularRating($jogador),
                    'inField' => true
                ];
            }
        }

        // Se só tem 1 equipe, cria uma segunda vazia para comparação
        if (count($teamsData) === 1) {
            $teamsData['B'] = [];
        }

        // Se não tem nenhuma equipe, cria duas vazias
        if (count($teamsData) === 0) {
            $teamsData['A'] = [];
            $teamsData['B'] = [];
        }

        // Limita a 2 equipes na visualização (A e B)
        $teamsData = array_slice($teamsData, 0, 2, true);

        // Converte para JSON para usar no JavaScript
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
        // Você pode implementar uma lógica mais complexa aqui
        // Por enquanto, retorna um valor entre 7.0 e 9.5
        $base = 7.0;

        // Adiciona pontos por altura (para zagueiros/goleiros)
        if (in_array($jogador->posicao_principal, ['Goleiro', 'Zagueiro'])) {
            if ($jogador->altura_cm >= 180) {
                $base += 0.5;
            }
        }

        // Adiciona pontos por juventude (jogadores mais novos)
        if ($jogador->pessoa && $jogador->pessoa->data_nascimento) {
            $idade = $jogador->pessoa->data_nascimento->age;
            if ($idade < 20) {
                $base += 0.3;
            }
        }

        // Adiciona variação aleatória
        $base += (mt_rand(0, 15) / 10);

        return min(9.9, $base);
    }
}
