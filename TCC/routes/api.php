<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PeneiraController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\PublicController;

// --- Rotas Públicas (Sem Token) ---
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    
    // Registro de Candidato (Público)
    Route::get('peneiras/open', [PublicController::class, 'getOpenPeneiras']);
    Route::post('register/candidate', [PublicController::class, 'registerCandidate']);
});

// --- Rotas Protegidas (Sanctum) ---
// [CORREÇÃO] Mudamos de 'auth:api' para 'auth:sanctum'
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // === ROTAS COMUNS (ADMIN E JOGADOR PODEM ACESSAR) ===
    // Aqui ficam apenas os GETs (Visualização)
    
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Visualizar Jogadores
    Route::get('players', [PlayerController::class, 'index']);
    Route::get('players/{id}', [PlayerController::class, 'show']);

    // Visualizar Peneiras
    Route::get('peneiras', [PeneiraController::class, 'index']);
    Route::get('peneiras/{id}', [PeneiraController::class, 'show']);
    
    // Visualizar Times
    Route::get('peneiras/{id}/teams', [TeamController::class, 'index']);


    // === ROTAS EXCLUSIVAS DE ADMINISTRADOR (ESCRITA) ===
    Route::middleware('role:adm')->group(function () {
        
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Modificar Jogadores (Criar, Editar, Excluir)
        Route::post('players', [PlayerController::class, 'store']);
        Route::put('players/{id}', [PlayerController::class, 'update']);
        Route::delete('players/{id}', [PlayerController::class, 'destroy']);
        Route::post('players/{id}/upload-photo', [PlayerController::class, 'uploadPhoto']);

        // Modificar Peneiras
        Route::post('peneiras', [PeneiraController::class, 'store']);
        Route::put('peneiras/{id}', [PeneiraController::class, 'update']);
        Route::delete('peneiras/{id}', [PeneiraController::class, 'destroy']);

        // Gerar/Salvar Times
        Route::post('peneiras/{id}/teams/generate', [TeamController::class, 'generate']);
        Route::post('peneiras/{id}/teams/save', [TeamController::class, 'store']);
    });
});