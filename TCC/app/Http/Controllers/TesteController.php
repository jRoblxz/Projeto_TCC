<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;
use App\Models\Avaliacao;
use App\Models\Views\DadosJogador;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class TesteController
{
    public function index()
    {

        $jogadores = DadosJogador::orderByDesc('jogador_id')->get();

        return view('home', ['jogadores' => $jogadores]);
    }

    public function show(Jogadores $jogadores)
    {
        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);
        return view('home', ['jogador' => $jogadores]);
    }
    public function edit($id)
    {
        // Buscar o jogador com os dados da pessoa relacionada
        $jogador = Jogadores::with('pessoa')->findOrFail($id);

        // OU se você quer usar a view DadosJogador (recomendado)
        // $jogador = DadosJogador::where('jogador_id', $id)->firstOrFail();

        return view('telas_crud.player_edit', ['jogador' => $jogador]);
    }

    public function update(UserRequest $request, $id)
    {
        $request->validated();

        // Buscar o jogador e pessoa
        $jogador = Jogadores::with('pessoa')->findOrFail($id);
        $pessoa = $jogador->pessoa;

        // Atualizar dados da pessoa
        $pessoa->update([
            'nome_completo' => $request->nome_completo,
            'data_nascimento' => $request->data_nascimento,
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'foto_perfil_url' => $request->foto_perfil_url,
        ]);

        // Atualizar dados do jogador
        $jogador->update([
            'pe_preferido' => $request->pe_preferido,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            'historico_lesoes_cirurgias' => $request->historico_lesoes_cirurgias,
            'altura_cm' => $request->altura_cm,
            'peso_kg' => $request->peso_kg,
            'video_apresentacao_url' => $request->video_apresentacao_url,
        ]);

        // Redirecionar para a página do jogador
        return redirect()->route('jogadores.info', $id)->with('success', 'Jogador atualizado com sucesso!');
    }

    /* ESSE TRECHO COMENTADO E O SEU KAYANAN, ESSE DE CIMA ESTOU TESTANDO
    public function edit(Pessoas $pessoas, Jogadores $jogadores)
    {
        
        // Carregar a VIEW do formulário de edição
        return view('users.edit', ['pessoas' => $pessoas, 'jogadores' => $jogadores]);
    }

    public function update(UserRequest $request, Pessoas $pessoas, Jogadores $jogadores)
    {
        // Validar e atualizar os dados do formulário
        $request->validated();

        // Atualizar o jogador no banco de dados
        $pessoas->update([
            'nome_completo' => $request->nome_completo,
            #'idade' => $request->idade,
            'data_nascimento' => $request->data_nascimento,
            #'cidade' => $request->cidade,
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'foto_perfil_url' => $request->foto_perfil_url,
        ]);

        $jogadores->update([
            'pessoa_id' => $pessoas->id,
            'pe_preferido' => $request->pe_preferido,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            'historico_lesoes_cirurgias' => $request->historico_lesoes_cirurgias,
            'altura_cm' => $request->altura_cm,
            'peso_kg' => $request->peso_kg,
            'video_apresentacao_url' => $request->video_apresentacao_url,
        ]);

        // Redirecionar para a lista de jogadores com uma mensagem de sucesso
        return redirect()->route('users.show', ['pessoas'=> $pessoas->id])->with('success', 'Jogador atualizado com sucesso!');
    }*/

    public function destroy(Pessoas $pessoas)
    {
        // Deletar o jogador do banco de dados
        $pessoas->delete();

        // Redirecionar para a lista de jogadores com uma mensagem de sucesso
        return redirect()->route('users.index')->with('success', 'Jogador deletado com sucesso!');
    }
}

/*
class AdmController{
    public function index()
    {
        // Usar a view que já tem todas as informações
        $players = DB::table('vw_perfil_jogador')->get();
        $avaliacao = DB::table('Avaliacoes')->get();
        
        return view('telas_crud.players', compact('players','avaliacao'));
    }
    
    public function destroy($id)
    {
        try {
            // Para DELETE, usar a tabela original Jogadores
            $player = Player::findOrFail($id);
            
            // Deletar avaliações relacionadas primeiro
            Avaliacao::where('jogador_id', $id)->delete();
            
            // Deletar o jogador
            $player->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Jogador deletado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar jogador: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            // Para UPDATE, usar a tabela original Jogadores
            $player = Player::findOrFail($id);
            
            $player->update($request->only([
                'pe_preferido',
                'posicao_principal', 
                'posicao_secundaria',
                'altura_cm',
                'peso_kg',
                'historico_lesoes_cirurgias',
                'video_apresentacao_url',
                'video_skills_url'
            ]));
            
            return response()->json([
                'success' => true,
                'message' => 'Jogador atualizado com sucesso!',
                'data' => $player
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar jogador: ' . $e->getMessage()
            ], 500);
        }
    }
}
*/