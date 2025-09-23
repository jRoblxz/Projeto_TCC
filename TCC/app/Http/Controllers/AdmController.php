<?php

namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;

use Illuminate\Http\Request;

class AdmController
{
    public function index()
    {
        $jogadores = Jogadores::orderByDesc('id')->get(); // Obter todos os usuários do banco de dados
        
        
        // Carregar a VIEW de Jogadores
        return view('telas_crud.players',['jogadores' => $jogadores]);
    }
/*
    public function show(Jogadores $user) #alterar depois os parametros para Pessoas e Jogadores
    {
        // Carregar a VIEW de detalhes do jogador
        return view('users.show', ['user' => $user]);
    }

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
    }

    public function destroy(Pessoas $pessoas)
    {
        // Deletar o jogador do banco de dados
        $pessoas->delete();

        // Redirecionar para a lista de jogadores com uma mensagem de sucesso
        return redirect()->route('users.index')->with('success', 'Jogador deletado com sucesso!');
    }
*/
}
