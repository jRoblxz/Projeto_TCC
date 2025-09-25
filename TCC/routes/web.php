<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdmController;
use App\Http\Controllers\TesteController;
use Illuminate\Support\Facades\Route;

// ROTAS DE ADMINISTRAÇÃO DE JOGADORES (CRUD) - OK
Route::get('/players', [AdmController::class, 'index'])->name('jogadores.index'); #tela de visualização de usuários -> (ok)
Route::get('/player_info/{jogadores}', [AdmController::class, 'show'])->name('jogadores.info'); #tela de detalhes de um usuário específico (ok)
Route::get('/player_edit/{jogadores}', [AdmController::class, 'edit'])->name('jogadores.edit'); #tela de edição de um usuário específico (ok)
//------------------------------------------------------------------------------------------------------------



// ROTAS DE EDIÇÃO E DELEÇÃO DE JOGADORES (CRUD) - não está funcionando a deleção e edição
Route::put('/player_upd/{jogadores}', [AdmController::class, 'update'])->name('jogadores.update');
Route::delete('/destroy-user/{jogadores}', [AdmController::class, 'destroy'])->name('jogadores.delete');
//------------------------------------------------------------------------------------------------------------




// ROTAS DE DE INSCRIÇÃO DE JOGADOR FUNCIONANDO CORRETAMENTE
Route::get('/forms1', [UserController::class, 'create'])->name('users.create');
Route::post('/forms1', [UserController::class, 'store'])->name('users.store');
Route::get('/confirmacao', [UserController::class, 'confirmacao'])->name('tela.confirmacao');
Route::get('/instrucao', [UserController::class, 'instrucao'])->name('tela.instrucao');
// --------------------------------------------------------------------------------------------






#ROTAS TESTE JOÃO 
/*
Route::get('/player_info', function () {
    return view('telas_crud.player_info');
});

Route::get('/player_edit', function () {
    return view('telas_crud.player_edit');
});
*/
Route::get('/home', [TesteController::class, 'index'])->name('home.index'); #tela de visualização de usuários -> (ok) falta as imagens

/*
#Route::get('/home', function () {
 #   return view('home');
#})->name('home');;

Route::get('/', function () {
    return view('login');
});
*/