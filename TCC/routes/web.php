<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index'])->name('users.index'); #tela de visualização de usuários

# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
Route::get('/show-user/{#}', [UserController::class, 'show'])->name('users.show'); #tela de visualização de um usuário específico - PRECISA ALTERAR O {#} PARA O NOME DA TABELA DO BANCO DE DADOS(LETRA MINÚSCULA E NO SINGULAR)

Route::get('/form1', [UserController::class, 'create'])->name('users.create'); #tela do formulário de criação de usuário
Route::post('/form1', [UserController::class, 'store'])->name('users.store'); #rota para salvar o novo usuário


#TELA DE TESTE - JOÃO ROBLEZ VAI VER ISSO AQUI
Route::get('/form1', function () {
    return view('telas_forms.forms1');
});

Route::get('/index', function () {
    return view('telas_forms.index');
});