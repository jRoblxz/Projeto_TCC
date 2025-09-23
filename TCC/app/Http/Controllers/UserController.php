<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;

class UserController
{

    public function create()
    {
        // Carregar a VIEW do formulário
        return view('telas_forms.forms1');
    }

    public function store(UserRequest $request)
    {
        // Validar e salvar os dados do formulário
        $request->validated();

        // Cadastrar o jogador no banco de dados
        $pessoas = Pessoas::create([
            'nome_completo' => $request->nome_completo,
            #'idade' => $request->idade,
            'data_nascimento' => $request->data_nascimento,
            #'cidade' => $request->cidade, #deve adicionar no banco de dados
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'foto_perfil_url' => $request->foto_perfil_url,

        ]);

        $jogadores = Jogadores::create([
            'pessoa_id' => $pessoas->id,
            'pe_preferido' => $request->pe_preferido,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            'historico_lesoes_cirurgias' => $request->historico_lesoes_cirurgias,
            'altura_cm' => $request->altura_cm,
            'peso_kg' => $request->peso_kg,
            'video_apresentacao_url' => $request->video_apresentacao_url, #video_skills_url pode ser deletado no banco
        ]);

        // Redirecionar para a lista de jogadores com uma mensagem de sucesso
        return redirect()->route('telas_forms.instrucao')->with('success', 'Jogador cadastrado com sucesso!');
    }
}
