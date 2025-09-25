<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdmController;
use App\Http\Controllers\TesteController;
use Illuminate\Support\Facades\Route;


Route::get('/players', [AdmController::class, 'index'])->name('jogadores.index'); #tela de visualização de usuários -> (ok)
Route::get('/player_info/{jogadores}', [AdmController::class, 'show'])->name('jogadores.info'); #tela de detalhes de um usuário específico (ok)


Route::get('/player_edit/{jogadores}', [AdmController::class, 'edit'])->name('jogadores.edit');
Route::put('/player_upd/{jogador}', [AdmController::class, 'update'])->name('jogadores.update');

Route::delete('/destroy-user/{pessoas}', [AdmController::class, 'destroy'])->name('jogadores.delete');


# esse trecho e antigo
# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
#Route::get('/edit-user/{pessoas}/{jogadores}', [AdmController::class, 'edit'])->name('users.edit'); #tela de edição de um usuário específico



# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
#Route::put('/update-user/{pessoas}/{jogadores}', [AdmController::class, 'update'])->name('users.update'); #tela de atualizar de um usuário específico

# Rota que falta ser criada na view, assim que criada alterar o nome da rota aqui e na controller
#Esse Rota vai na view users.index
#Route::delete('/destroy-user/{pessoas}', [AdmController::class, 'destroy'])->name('users.destroy');



// ROTAS DE DE INSCRIÇÃO DE JOGADOR FUNCIONANDO CORRETAMENTE
Route::get('/forms1', [UserController::class, 'create'])->name('users.create');
Route::post('/forms1', [UserController::class, 'store'])->name('users.store');
Route::get('/confirmacao', [UserController::class, 'confirmacao'])->name('tela.confirmacao');
Route::get('/instrucao', [UserController::class, 'instrucao'])->name('tela.instrucao');
// --------------------------------------------------------------------------------------------



Route::get('/home', [TesteController::class, 'index'])->name('home.index'); #tela de visualização de usuários -> (ok) falta as imagens


#ROTAS TESTE JOÃO 
/*
Route::get('/player_info', function () {
    return view('telas_crud.player_info');
});

Route::get('/player_edit', function () {
    return view('telas_crud.player_edit');
});


#Route::get('/home', function () {
 #   return view('home');
#})->name('home');;

Route::get('/', function () {
    return view('login');
});
*/