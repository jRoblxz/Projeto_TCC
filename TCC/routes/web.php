<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index'])->name('users.index'); #tela de visualização de usuários

# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
Route::get('/show-user/{pessoas}', [UserController::class, 'show'])->name('users.show'); #tela de visualização de um usuário específico - PRECISA ALTERAR O {#} PARA O NOME DA TABELA DO BANCO DE DADOS(LETRA MINÚSCULA E NO SINGULAR)

# esse trecho e antigo
# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
Route::get('/edit-user/{pessoas}/{jogadores}', [UserController::class, 'edit'])->name('users.edit'); #tela de edição de um usuário específico

#Usei essas 4 rotas para ver as trelas, apaga se necessario (joao)
Route::get('/players', function () {
    return view('telas_crud.players');
})->name('players');

Route::get('/player_info', function () {
    return view('telas_crud.player_info');
});

Route::get('/player_edit', function () {
    return view('telas_crud.player_edit');
});
Route::get('/home', function () {
    return view('dashboard');
})->name('home');;

Route::get('/login', function () {
    return view('login');
});


# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
Route::put('/update-user/{pessoas}/{jogadores}', [UserController::class, 'update'])->name('users.update'); #tela de atualizar de um usuário específico

# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
Route::delete('/destroy-user/{pessoas}', [UserController::class, 'destroy'])->name('users.destroy'); #tela de deletar de um usuário específico

Route::get('/forms1', [UserController::class, 'create'])->name('users.create'); #tela do formulário de criação de usuário
Route::post('/forms1', [UserController::class, 'store'])->name('users.store'); #rota para salvar o novo usuário



Route::get('/instrucao', function () {
    return view('telas_forms.instrucao');
});