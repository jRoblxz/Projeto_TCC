<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdmController;
use App\Http\Controllers\PeneiraController;
use App\Http\Controllers\LoginController; // Importa o controller de login
use App\Http\Controllers\EquipeController;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------

| Rotas que qualquer um pode acessar, mesmo sem estar logado.
|
*/

// ROTAS DE AUTENTICAÇÃO
// [CORREÇÃO] Rota para MOSTRAR a página de login (agora em /login)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// [CORREÇÃO] Rota para PROCESSAR o login (também em /login)
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// [NOVO] Rota raiz (/) agora redireciona para a página de login
Route::get('/', function () {
    return redirect()->route('login');
});

// ROTAS DE INSCRIÇÃO DE JOGADOR
Route::get('/forms1', [UserController::class, 'create'])->name('users.create');
Route::post('/forms1', [UserController::class, 'store'])->name('users.store');
Route::get('/confirmacao', [UserController::class, 'confirmacao'])->name('tela.confirmacao');
Route::get('/instrucao', [UserController::class, 'instrucao'])->name('tela.instrucao');

// ROTA PARA LÓGICA DE MONTAR EQUIPE
//Route::get('/peneiras/{id}/montar', [PeneiraController::class, 'montarindex'])->name('peneiras.montar');
//Route::post('/peneiras/{id}/montar-equipes', [EquipeController::class, 'montarEquipes'])->name('peneiras.montarEquipes');
// routes/web.php


// Gera automaticamente e vai para o editor
Route::post('/peneira/{id}/equipes/gerar', [EquipeController::class, 'gerarERedirecionarParaEditor'])
    ->name('equipes.gerar-e-editar');

// Apenas abre o editor (se já tiver equipes ou para criar manualmente)
Route::get('/peneira/{id}/equipes/montar', [EquipeController::class, 'editarEquipes'])
    ->name('equipes.tela-montagem');

// Salva as alterações feitas no editor
Route::post('/peneira/{id}/equipes/salvar', [EquipeController::class, 'salvarEquipes'])
    ->name('peneira.salvar-equipes');




// [CORREÇÃO] Rota movida para cá
// Tela onde o candidato vê os jogadores (conforme seu pedido)
Route::get('/players', [AdmController::class, 'Jogadores'])->name('jogadores.index');




/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS
|--------------------------------------------------------------------------
|
| O grupo 'auth' garante que SÓ usuários logados (com sessão)
| podem acessar estas rotas.
|
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ROTAS DE ADMINISTRADOR (role:adm)
    |--------------------------------------------------------------------------
    |
    | O middleware 'role:adm' garante que SÓ o Admin pode ver isso.
    |
    */
    Route::middleware(['role:adm'])->group(function () {

        Route::get('/peneira_id', [AdmController::class, 'index'])->name('peneira.index');
        Route::get('/player_info/{jogadores}', [AdmController::class, 'show'])->name('jogadores.info');
        Route::get('/player_edit/{jogadores}', [AdmController::class, 'edit'])->name('jogadores.edit');
        Route::put('/player_upd/{jogadores}', [AdmController::class, 'update'])->name('jogadores.update');
        Route::delete('/destroy-user/{jogadores}', [AdmController::class, 'destroy'])->name('jogadores.delete');

        // ROTAS DE DASHBOARD / HOMEPAGE
        Route::get('/home', [AdmController::class, 'homepage'])->name('home.index');

        // ROTAS DE PENEIRAS (CRUD Completo)
        Route::resource('peneiras', PeneiraController::class);

        // [NOTA] A rota '/players' foi MOVIDA para o grupo de candidato
    });


    /*
    |--------------------------------------------------------------------------
    | ROTAS DE CANDIDATO (role:candidato)
    |--------------------------------------------------------------------------
    |
    | O middleware 'role:candidato' garante que SÓ o Candidato pode ver isso.
    |
    */
    Route::middleware(['role:candidato'])->group(function () {

        // Ex: Rota para o candidato ver seu próprio perfil
        Route::get('/meu-perfil', [UserController::class, 'showMyProfile'])->name('candidato.profile');
    });
});
