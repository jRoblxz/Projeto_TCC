<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('id')->get(); // Obter todos os usuários do banco de dados
        
        
        // Carregar a VIEW de Jogadores
        return view('users.index',['users' => $users]);
    }

    public function show(User $user) #alterar depois os parametros para Pessoas e Jogadores
    {
        // Carregar a VIEW de detalhes do jogador
        return view('users.show', ['user' => $user]);
    }

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
            'nome_completo' => $request->nome,
            'idade' => $request->idade,
            'data_nascimento' => $request->nasc,
            'cidade' => $request->cidade, #deve adicionar no banco de dados
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'email' => $request->email,
            'telefone' => $request->fone,
            'foto_perfil_url' => $request->img,

        ]);

        $jogadores = Jogadores::create([
            'id_pessoa' => $pessoas->id,
            'pe_preferido' => $request->pe,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            'historico_lesoes_cirurgias' => $request->cirurgia,
            'altura_cm' => $request->altura,
            'peso_kg' => $request->peso,
            'video_apresentacao_url' => $request->link, #video_skills_url pode ser deletado no banco
        ]);
        



        // Redirecionar para a lista de jogadores com uma mensagem de sucesso
        return redirect()->route('tela_forms.instrucao')->with('success', 'Jogador cadastrado com sucesso!');
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
            'nome_completo' => $request->nome,
            'idade' => $request->idade,
            'data_nascimento' => $request->nasc,
            'cidade' => $request->cidade,
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'email' => $request->email,
            'telefone' => $request->fone,
            'foto_perfil_url' => $request->img,
        ]);

        $jogadores->update([
            'id_pessoa' => $pessoas->id,
            'pe_preferido' => $request->pe,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            'historico_lesoes_cirurgias' => $request->cirurgia,
            'altura_cm' => $request->altura,
            'peso_kg' => $request->peso,
            'video_apresentacao_url' => $request->link,
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
}
