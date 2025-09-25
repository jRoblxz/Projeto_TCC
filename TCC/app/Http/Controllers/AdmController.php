<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;
use App\Models\Avaliacao;
use App\Models\Views\DadosJogador;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PDO;

class AdmController
{
    public function index()
    {

        $jogadores = DadosJogador::orderByDesc('jogador_id')->get();

        return view('telas_crud.players', ['jogadores' => $jogadores]);
    }

    public function show(Jogadores $jogadores)
    {
        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);
        return view('telas_crud.player_info', ['jogador' => $jogadores]);
    }
    public function edit(Jogadores $jogadores)
    {

        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);
        return view('telas_crud.player_edit', ['jogador' => $jogador]);
    }

    public function update(UserRequest $request, Jogadores $jogador)
    {
        
        $request->validated();

        // Atualizar dados da pessoa
        $jogador->pessoa->update([
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

        return redirect()->route('jogadores.info', ['jogadores' => $jogador->id]);

    }

    public function destroy(Pessoas $pessoas)
    {
        // Encontrar o jogador associado à pessoa
        $jogador = Jogadores::where('pessoa_id', $pessoas->id);

        if ($jogador) {
            // Deletar avaliações associadas ao jogador, se houver
            Avaliacao::where('jogador_id', $jogador->id)->delete();
            // Deletar o jogador
            $jogador->delete();
        }

        // Deletar a pessoa
        $pessoas->delete();

        return redirect()->route('jogadores.index');
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

}