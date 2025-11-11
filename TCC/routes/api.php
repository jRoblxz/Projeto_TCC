<?php

use App\Http\Controllers\AdmController;
use App\Http\Controllers\PeneiraController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rotas de API são stateless (usam TOKENS, não sessões).
| O Laravel adiciona o prefixo '/api' automaticamente.
|
*/

// --- Rotas de Autenticação (Abertas) ---
// O usuário vai chamar POST /api/login
Route::post('login', [AuthController::class, 'login']);

// --- Rotas Protegidas por JWT (Precisa estar logado) ---
Route::middleware('auth:api')->group(function () {
    
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

    // --- Rotas de Administrador (só 'adm' pode ver) ---
    Route::middleware('role:adm')->group(function () {
        
        // ROTAS DE PENEIRAS (O resource já cria .index, .show, .store, .update, .destroy)
        Route::resource('peneiras', PeneiraController::class);

        // ROTA PARA LÓGICA DE MONTAR EQUIPE
        Route::post('/peneiras/{id}/montar-equipes', [EquipeController::class, 'montarEquipes'])->name('peneiras.montarEquipes');

        // ROTAS DE ADMINISTRAÇÃO DE JOGADORES
        Route::get('/players', [AdmController::class, 'Jogadores'])->name('jogadores.index');
        Route::get('/player_info/{jogadores}', [AdmController::class, 'show'])->name('jogadores.info');
        Route::get('/player_edit/{jogadores}', [AdmController::class, 'edit'])->name('jogadores.edit');
        Route::put('/player_upd/{jogadores}', [AdmController::class, 'update'])->name('jogadores.update');
        Route::delete('/destroy-user/{jogadores}', [AdmController::class, 'destroy'])->name('jogadores.delete');

        // ROTAS DE DASHBOARD / HOMEPAGE
        Route::get('/home', [AdmController::class, 'homepage'])->name('home.index');

        // [PROBLEMA CORRIGIDO]
        // Esta rota estava duplicada e conflitava com o Route::resource
        // Route::get('/peneiras/{id}', [PeneiraController::class, 'show'])->name('peneira.show'); // REMOVIDA
    });

    // --- Rotas de Candidato (só 'candidato' pode ver) ---
    Route::middleware('role:candidato')->group(function () {
        
        // Ex: Route::get('/minha-inscricao', [CandidatoController::class, 'show']);
        
    });
});