<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('frontend.home');
});

Route::get('/players', function () {
    return view('telas_crud.players');
});

Route::get('/form1', function () {
    return view('telas_forms.forms1');
});

Route::get('/index', function () {
    return view('telas_forms.index');
});

Route::get('/dashboard', function () {
    // Dados fake só para teste
    $usuario = "Usuário Teste";
    return view('frontend.dashboard', compact('usuario'));
});