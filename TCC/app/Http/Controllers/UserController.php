<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Inscricoes;
use App\Models\Jogadores;
use App\Models\Pessoas;
use App\Models\Peneiras;
use Carbon\Carbon;

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
        $peneiras = Peneiras::where('status', 'agendada')->get();

        return view('telas_forms.forms1', compact('peneiras'));
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

        $inscricao = Inscricoes::create([
            'jogador_id' => $jogadores->id,
            'peneira_id' => $request->peneira_id,
            'data_inscricao' => Carbon::now(),
        ]);

        // Redirecionar para a tela de confirmação
        return view('telas_forms.confirmacao', compact('inscricao'));

    }
}
