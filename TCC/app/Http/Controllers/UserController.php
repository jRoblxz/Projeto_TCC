<?php

namespace App\Http\Controllers;

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

    public function store()
    {
        
    }
}
