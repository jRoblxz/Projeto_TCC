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

    // Gerenciamento de Autenticação
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']); // Nota: refresh é manual no Sanctum
    Route::get('me', [AuthController::class, 'me']);

    // --- Rotas de Administrador ---
    // Certifique-se de que você tem o middleware 'role' registrado no bootstrap/app.php
    Route::middleware('role:adm')->group(function () {
        
        // Dashboard Stats
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Players CRUD
        Route::apiResource('players', PlayerController::class);
        Route::post('players/{id}/upload-photo', [PlayerController::class, 'uploadPhoto']);

        // Peneiras CRUD
        Route::apiResource('peneiras', PeneiraController::class);

        // Teams (Lógica para o React Drag-and-Drop)
        Route::get('peneiras/{id}/teams', [TeamController::class, 'index']);     // Buscar times
        Route::post('peneiras/{id}/teams/generate', [TeamController::class, 'generate']); // Gerar Auto
        Route::post('peneiras/{id}/teams/save', [TeamController::class, 'store']);    // Salvar Manual
    });
});