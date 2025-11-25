<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;
use App\Models\Avaliacao;
use App\Models\Views\DadosJogador;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PDO;
use Illuminate\Support\Facades\Storage;

class AdmController
{
    //tela/rota  da view Players
    public function index()
    {

        $jogadores = Jogadores::with('pessoa')->orderByDesc('id')->get();


        return view('telas_crud.players', ['jogadores' => $jogadores]);
    }

    public function Jogadores()
    {

        $jogadores = Jogadores::with('pessoa')->orderByDesc('id')->get();

        return view('telas_crud.players', ['jogadores' => $jogadores]);
    }

    //tela/rota  da view Home
    public function homepage(Request $request)
    {
        // 1. Query Básica de Jogadores (Traz todos primeiro)
        $queryJogadores = Jogadores::with('pessoa')->orderByDesc('id')->get();

        // 2. Query de Peneiras (Esta continua via SQL pois a coluna existe na tabela peneiras)
        $queryPeneiras = \App\Models\Peneiras::query();

        // --- LÓGICA DE FILTRO CORRIGIDA ---
        if ($request->filled('subdivisao')) {
            $filtroSub = $request->subdivisao; // Ex: "Sub-13"

            // Filtrar Peneiras (SQL direto)
            $queryPeneiras->where('sub_divisao', $filtroSub);

            // Filtrar Jogadores (Via Coleção PHP)
            // Isso permite filtrar mesmo que 'sub_divisao' seja calculado pela data de nascimento
            $queryJogadores = $queryJogadores->filter(function ($jogador) use ($filtroSub) {
                // Verifica se a pessoa existe e se a subdivisão bate com o filtro
                return $jogador->pessoa && ($jogador->pessoa->sub_divisao == $filtroSub);
            });
        }

        // Atualiza a variável final de jogadores e a contagem baseada no filtro
        $jogadores = $queryJogadores; 
        $totalJogadores = $jogadores->count();

        // Busca as peneiras (já filtradas acima se necessário)
        $peneiras = $queryPeneiras->orderByDesc('data_evento')->get();
        
        // Separa peneiras por status
        $peneirasAtivas = $peneiras->whereIn('status', ['EM_ANDAMENTO', 'AGENDADA']);
        $peneirasFinalizadas = $peneiras->where('status', 'FINALIZADA');

        // Estatísticas
        $stats = [
            'total_candidatos' => $totalJogadores,
            'peneiras_ativas' => $peneirasAtivas->count(),
            'aprovados' => 0, 
            'em_avaliacao' => 0,
            'avaliadores' => \App\Models\User::where('role', 'avaliador')->count(),
        ];

        return view('home', [
            'jogadores' => $jogadores, // Passa a coleção filtrada
            'totalJogadores' => $totalJogadores,
            'peneiras' => $peneiras,
            'peneirasAtivas' => $peneirasAtivas,
            'stats' => $stats,
        ]);
    }

    //tela/rota da view Player_info
    public function show(Jogadores $jogadores)
    {
        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);
        return view('telas_crud.player_info', ['jogador' => $jogador]);
    }
    //tela/rota da view Player_edit
    public function edit(Jogadores $jogadores)
    {

        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);
        return view('telas_crud.player_edit', ['jogador' => $jogador]);
    }

    //Update da crud
    public function update(Request $request, Jogadores $jogadores)
    {

        $request->validate([
            'nome_completo' => 'required|string|max:255',
            'altura_cm' => 'nullable|numeric',
            'peso_kg' => 'nullable|numeric',
            'pe_preferido' => 'nullable|string',
            'posicao_principal' => 'nullable|string',
            'posicao_secundaria' => 'nullable|string',
            //  'historico_lesoes_cirurgias' => 'nullable|string',
            //  'video_apresentacao_url' => 'nullable|url',
            // 'foto_perfil_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Buscar o jogador com pessoa
        $diskName = 'gcs';
        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);


        // Upload da imagem se enviada
        $fotoPath = $jogador->pessoa->foto_perfil_url; // Manter a atual
        if ($request->hasFile('image')) {
            // Deletar imagem antiga se existir
            if ($fotoPath && Storage::disk($diskName)->exists($fotoPath)) {
                Storage::disk($diskName)->delete($fotoPath);
            }

            // Salvar nova imagem
            $fotoPath = $request->file('image')->store('user', $diskName);
        }

        // Atualizar dados da pessoa
        $jogador->pessoa->update([
            'nome_completo' => $request->nome_completo,
            // 'data_nascimento' => $request->data_nascimento,
            //   'cpf' => $request->cpf,
            //  'rg' => $request->rg,
            //  'email' => $request->email,
            //   'telefone' => $request->telefone,
            'foto_perfil_url' => $fotoPath,
        ]);

        if ($request->has('rating_medio') && is_numeric($request->rating_medio)) {
            // 1. Encontra a última avaliação existente para este jogador
            $ultimaAvaliacao = \App\Models\Avaliacao::where('jogador_id', $jogador->id)
                ->orderByDesc('data_avaliacao') // Assume que você tem uma coluna data_avaliacao na tabela Avaliacoes
                ->first();

            // 2. Se existe uma avaliação, atualiza ela. Se não, cria uma nova.
            if ($ultimaAvaliacao) {
                $ultimaAvaliacao->update([
                    'nota' => $request->rating_medio,
                    'data_avaliacao' => now(), // Atualiza a data para trazê-la para o topo
                ]);
            } else {
                // Cria uma nova avaliação se não existir nenhuma (usando um ID de treinador genérico ou o ID do ADM logado)
                \App\Models\Avaliacao::create([
                    'jogador_id' => $jogador->id,
                    'treinador_id' => Auth::id() ?? 1, // Use o ID do ADM ou um valor padrão (1)
                    'nota' => $request->rating_medio,
                    'observacoes' => 'Rating editado pelo ADM.',
                    'data_avaliacao' => now(),
                ]);
            }

            // 3. O rating médio do jogador será recalculado automaticamente pela Accessor
        }

        // Atualizar dados do jogador
        $jogador->update([
            'pe_preferido' => $request->pe_preferido,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            // 'historico_lesoes_cirurgias' => $request->historico_lesoes_cirurgias,
            'altura_cm' => $request->altura_cm,
            'peso_kg' => $request->peso_kg,
            //  'video_apresentacao_url' => $request->video_apresentacao_url,
        ]);

        // Se for uma requisição AJAX (do modal de rating)
        if ($request->ajax()) {
            // Recalcula o novo rating médio
            $novoRating = $jogador->fresh()->getRatingMedioAttribute(); // Recarrega o modelo e recalcula o rating

            return response()->json([
                'success' => true,
                'message' => 'Rating atualizado com sucesso!',
                'jogador_id' => $jogador->id,
                'novo_rating' => $novoRating,
            ]);
        }

        return redirect()->route('jogadores.info', ['jogadores' => $jogador->id])
            ->with('success', 'Jogador atualizado com sucesso!');;
    }

    //Delete da crud
    public function destroy(Jogadores $jogadores)
    {
        try {
            $diskName = 'gcs';

            // O jogador já é injetado pela rota
            $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);

            // Deletar foto se existir
            // Deletar foto se existir NO GCS
            $fotoPath = $jogador->pessoa->foto_perfil_url;
            if ($fotoPath && Storage::disk($diskName)->exists($fotoPath)) {
                Storage::disk($diskName)->delete($fotoPath);
            }

            // Deletar avaliações relacionadas primeiro
            Avaliacao::where('jogador_id', $jogador->id)->delete();

            // Salvar pessoa para deletar depois
            $pessoa = $jogador->pessoa;

            // Deletar o jogador primeiro
            // $jogador->delete();

            // Deletar a pessoa
            $jogador->pessoa->delete();

            return redirect()->back()
                ->with('success', 'Jogador deletado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao deletar jogador: ' . $e->getMessage());
        }
    }
}
