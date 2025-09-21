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
    }
}
