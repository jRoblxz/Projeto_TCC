<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdmController;
use App\Http\Controllers\PeneiraController;
use Illuminate\Support\Facades\Route;

// ROTAS DE ADMINISTRAÇÃO DE JOGADORES (CRUD) - OK
Route::get('/peneira_id', [AdmController::class, 'index'])->name('peneira.index'); #tela de visualização de usuários -> (ok)
Route::get('/player_info/{jogadores}', [AdmController::class, 'show'])->name('jogadores.info'); #tela de detalhes de um usuário específico (ok)
Route::get('/player_edit/{jogadores}', [AdmController::class, 'edit'])->name('jogadores.edit'); #tela de edição de um usuário específico (ok)
//------------------------------------------------------------------------------------------------------------

// ROTAS DE EDIÇÃO E DELEÇÃO DE JOGADORES (CRUD) - não está funcionando a deleção e edição
Route::put('/player_upd/{jogadores}', [AdmController::class, 'update'])->name('jogadores.update');
Route::delete('/destroy-user/{jogadores}', [AdmController::class, 'destroy'])->name('jogadores.delete');
//------------------------------------------------------------------------------------------------------------

// ROTAS DE DASHBOARD / HOMEPAGE
Route::get('/home', [AdmController::class, 'homepage'])->name('home.index');

// ROTAS DE DE INSCRIÇÃO DE JOGADOR
Route::get('/forms1', [UserController::class, 'create'])->name('users.create');
Route::post('/forms1', [UserController::class, 'store'])->name('users.store');
Route::get('/confirmacao', [UserController::class, 'confirmacao'])->name('tela.confirmacao');
Route::get('/instrucao', [UserController::class, 'instrucao'])->name('tela.instrucao');
// --------------------------------------------------------------------------------------------

// ROTAS DE PENEIRAS
Route::resource('peneiras', PeneiraController::class);




Route::get('/peneira', function () { return view('telas_crud.peneira');})->name('tela.peneira');

Route::get('/players', [AdmController::class, 'Jogadores'])->name('jogadores.index'); #tela de visualização de usuários -> (ok)

//Route::get('/playes, function () {return view('telas_crud.players');})->name('playes.index');


#ROTAS frontend JOÃO 
/*
Route::get('/player_info', function () {
    return view('telas_crud.player_info');
});

Route::get('/peneira', function () {
    return view('telas_crud.peneira');
});
*/

/*
#Route::get('/home', function () {
 #   return view('home');
#})->name('home');;

Route::get('/', function () {
    return view('login');
});
*/