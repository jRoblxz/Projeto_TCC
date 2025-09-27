<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;
use App\Models\Avaliacao;
use App\Models\Views\DadosJogador;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class TesteController
{
    public function index()
    {

        $jogadores = Jogadores::with('pessoa')->orderByDesc('id')->get();
        $totalJogadores= Jogadores::count(); //contagem de jogadores

        return view('home', ['jogadores' => $jogadores, 'totalJogadores' => $totalJogadores]);
    }

    public function show(Jogadores $jogadores)
    {
        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);
        return view('home', ['jogador' => $jogador]);
    }
    
}