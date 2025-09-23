<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdmController;
use Illuminate\Support\Facades\Route;

Route::get('/players', [AdmController::class, 'index'])->name('players.index'); #tela de visualização de usuários
Route::delete('/players/{id}', [AdmController::class, 'destroy'])->name('players.destroy');

# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
Route::get('/show-user/{pessoas}', [AdmController::class, 'show'])->name('users.show'); #tela de visualização de um usuário específico - PRECISA ALTERAR O {#} PARA O NOME DA TABELA DO BANCO DE DADOS(LETRA MINÚSCULA E NO SINGULAR)

# esse trecho e antigo
# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
Route::get('/edit-user/{pessoas}/{jogadores}', [AdmController::class, 'edit'])->name('users.edit'); #tela de edição de um usuário específico



# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
Route::put('/update-user/{pessoas}/{jogadores}', [AdmController::class, 'update'])->name('users.update'); #tela de atualizar de um usuário específico

# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
Route::delete('/destroy-user/{pessoas}', [AdmController::class, 'destroy'])->name('users.destroy'); #tela de deletar de um usuário específico

Route::get('/forms1', [UserController::class, 'create'])->name('users.create'); #tela do formulário de criação de usuário -> (OK)
Route::post('/forms1', [UserController::class, 'store'])->name('users.store'); #rota para salvar o novo usuário -> (OK)


#ROTAS TESTE JOÃO 
Route::get('/instrucao', function () {
    return view('telas_forms.instrucao');
});

#Route::get('/players', function () {
   # return view('telas_crud.players');
#})->name('players');

Route::get('/player_info', function () {
    return view('telas_crud.player_info');
});

Route::get('/player_edit', function () {
    return view('telas_crud.player_edit');
});
Route::get('/home', function () {
    return view('home');
})->name('home');;

Route::get('/', function () {
    return view('login');
});

Route::get('/confirm', function () {
    return view('telas_forms.confirmacao');
});
