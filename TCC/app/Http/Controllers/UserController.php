<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('id')->get(); // Obter todos os usuários do banco de dados
        
        
        // Carregar a VIEW de Jogadores
        return view('users.index',['users' => $users]);
    }

    public function show(User $user)
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
        User::create([
            'nome' => $request->nome,
            'idade' => $request->idade,
            'nasc' => $request->nasc,
            'cidade' => $request->cidade,
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'email' => $request->email,
            'fone' => $request->fone,
            'altura' => $request->altura,
            'peso' => $request->peso,
            'img' => $request->img,
            'link' => $request->link,
            'pe' => $request->pe,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            'cirurgia' => $request->cirurgia,
        ]);

        // Redirecionar para a lista de jogadores com uma mensagem de sucesso
        return redirect()->route('users.index')->with('success', 'Jogador cadastrado com sucesso!');
    }
}
