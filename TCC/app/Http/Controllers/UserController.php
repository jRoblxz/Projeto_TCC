<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;

class UserController
{

    public function instrucao()
    {
        return view('telas_forms.instrucao');
    }

    public function confirmacao()
    {
        return view('telas_forms.confirmacao');
    }

    public function create()
    {
        return view('telas_forms.forms1');
    }

    public function store(UserRequest $request)
    {
        // Validar e salvar os dados do formulário
        $request->validated();

        $path = null;
        if ($request->hasFile('foto_perfil_url')) {
            $path = $request->file('foto_perfil_url')->store('user' ,'public');
        }    

        // Cadastrar o jogador no banco de dados
        $pessoas = Pessoas::create([
            'nome_completo' => $request->nome_completo,
            'data_nascimento' => $request->data_nascimento,
            'cidade' => $request->cidade,
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'foto_perfil_url' => $path,

        ]);

        $jogadores = Jogadores::create([
            'pessoa_id' => $pessoas->id,
            'pe_preferido' => $request->pe_preferido,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            'historico_lesoes_cirurgias' => $request->historico_lesoes_cirurgias,
            'altura_cm' => $request->altura_cm,
            'peso_kg' => $request->peso_kg,
            'video_apresentacao_url' => $request->video_apresentacao_url,
        ]);

        // Redirecionar para a tela de confirmação
        return redirect()->route('tela.confirmacao');

    }
}
