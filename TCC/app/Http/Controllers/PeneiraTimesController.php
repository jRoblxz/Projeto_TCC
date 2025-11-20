<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Peneiras;
use App\Models\Inscricoes;
use App\Models\Jogadores;
use App\Models\Equipe; // sua model Equipe (tabela Equipes)
use Carbon\Carbon;
use Faker\Provider\Base;

class PeneiraTimesController
{
    // mostra a tela de edição/drag & drop
    public function index(Peneiras $peneira)
    {
        // só permitir peneiras em andamento
        if (strtolower($peneira->status) !== 'em_andamento') {
            abort(403, 'Peneira não está em andamento.');
        }

        // carrega inscritos com dados do jogador e pessoa
        $inscritos = Inscricoes::where('peneira_id', $peneira->id)
            ->with('jogador.pessoa')
            ->get()
            ->map(fn($ins) => $ins->jogador); // retorna coleção de Jogadores

        // se quiser já carregar equipes previamente salvas:
        $equipes = Equipe::where('peneira_id', $peneira->id)->with('jogadores.pessoa')->get();

        return view('peneiras.times_editor', [
            'peneira' => $peneira,
            'inscritos' => $inscritos,
            'equipes' => $equipes
        ]);
    }

    // Gera times aleatorios (retorna JSON com estrutura para front)
    public function randomTeams(Request $request, Peneiras $peneira)
    {
        $inscritos = Inscricoes::where('peneira_id', $peneira->id)->with('jogador.pessoa')->get()->pluck('jogador')->toArray();

        // shuffle e divide
        shuffle($inscritos);

        // garante que haja pelo menos 2 jogadores por time? mínimo 2 times
        $half = (int) ceil(count($inscritos) / 2);
        $teamA = array_slice($inscritos, 0, $half);
        $teamB = array_slice($inscritos, $half);

        return response()->json([
            'success' => true,
            'teams' => [
                'A' => $teamA,
                'B' => $teamB
            ]
        ]);
    }

    // Salva os times no banco
    // Espera payload JSON: { teams: { A: [{id: jogador_id, pos: 'ST', slot: ...}, ...], B: [...] }, equipes_meta: { A: {nome: 'Time A'}, B: {...} } }
    public function saveTeams(Request $request, Peneiras $peneira)
    {
        $data = $request->validate([
            'teams' => 'required|array',
            'teams.A' => 'required|array',
            'teams.B' => 'required|array',
            'equipes_meta' => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            // opcional: apagar equipes antigas dessa peneira (ou marcar inativas)
            Equipe::where('peneira_id', $peneira->id)->delete();

            // criar Time A e Time B
            foreach (['A', 'B'] as $key) {
                $nomeEquipe = $request->input("equipes_meta.{$key}.nome") ?? "Time {$key}";
                $equipe = Equipe::create([
                    'nome_equipe' => $nomeEquipe,
                    'peneira_id' => $peneira->id
                ]);

                // salvar jogadores por equipe em tabela pivot 'JogadoresPorEquipe'
                // assumindo que a migration existe (veja abaixo)
                $players = $request->input("teams.{$key}", []);
                foreach ($players as $p) {
                    DB::table('JogadoresPorEquipe')->insert([
                        'equipe_id' => $equipe->id,
                        'jogador_id' => $p['id'],
                        'posicao' => $p['pos'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Times salvos com sucesso']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // View pra imprimir (ou gerar PDF)
    public function printTeams(Peneiras $peneira, $equipeGroup = null)
    {
        // buscar equipes da peneira
        $equipes = Equipe::where('peneira_id', $peneira->id)->with(['jogadores.pessoa'])->get();
        return view('peneiras.times_print', compact('peneira', 'equipes'));
    }
}
