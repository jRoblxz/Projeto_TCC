<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index()
    {
        // Carregar a VIEW de usuários
        return view('users.index');
    }

    public function create()
    {
        // Carregar a VIEW do formulário
        return view('users.create');
    }

    public function store(userRequest $request)
    {
        // Validar e salvar os dados do formulário
        $request->validated();

        User::create([
            'name' => $request->name,
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

        return redirect()->route('users.index')->with('success', 'Jogador cadastrado com sucesso!');
    }
}
