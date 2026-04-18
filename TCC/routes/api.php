<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PeneiraController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\VideoJobController;
use App\Http\Controllers\Api\WebhookController;

// --- Rotas Públicas (Sem Token) ---
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    
    // Registro de Candidato (Público)
    Route::get('peneiras/open', [PublicController::class, 'getOpenPeneiras']);
    Route::post('register/candidate', [PublicController::class, 'registerCandidate']);

    // Webhook do Modal (Sem autenticação, mas com secret)
    Route::post('/webhook/modal', [WebhookController::class, 'modal']);

    // Rota de cadastro de administradores/treinadores
    Route::post('/cadastro', [AuthController::class, 'register']);

    
});

// --- Rotas Protegidas (Sanctum) ---
// [CORREÇÃO] Mudamos de 'auth:api' para 'auth:sanctum'
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // === ROTAS COMUNS (ADMIN E JOGADOR PODEM ACESSAR) ===

    // Rota para o jogador ver peneiras da idade dele
    Route::get('/my-available-peneiras', [PublicController::class, 'getAvailableForMe']);
    // --> ADICIONE ESTA LINHA PARA A ROTA FUNCIONAR <--
    Route::get('/my-enrollments', [App\Http\Controllers\Api\PublicController::class, 'getMyEnrollments']);
    // Rota para se inscrever apenas enviando o ID da peneira
    Route::post('/enroll-again', [PublicController::class, 'quickEnroll']);
    
    // Aqui ficam apenas os GETs (Visualização)
    
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('/players-stats', [PlayerController::class, 'stats']);

    // Visualizar Jogadores
    Route::get('players', [PlayerController::class, 'index']);
    Route::get('players/{id}', [PlayerController::class, 'show']);

    // Visualizar Peneiras
    Route::get('peneiras', [PeneiraController::class, 'index']);
    Route::get('peneiras/{id}', [PeneiraController::class, 'show']);
    
    // Visualizar Times
    Route::get('peneiras/{id}/teams', [TeamController::class, 'index']);

    // Rota de cadastro de administradores/treinadores
    Route::post('/register', [AuthController::class, 'register']);


    // === ROTAS EXCLUSIVAS DE ADMINISTRADOR (ESCRITA) ===
    Route::middleware('auth:sanctum', 'role:adm,treinador')->group(function () {
        
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


        Route::post('/video-jobs',      [VideoJobController::class, 'store']);
        Route::get('/video-jobs',       [VideoJobController::class, 'index']);
        Route::get('/video-jobs/{videoJob}', [VideoJobController::class, 'show']);
        Route::post('/video-jobs/upload-url', [VideoJobController::class, 'getUploadUrl']);
        Route::put('/video-jobs/{videoJob}', [VideoJobController::class, 'update']);
        Route::delete('/video-jobs/{videoJob}', [VideoJobController::class, 'destroy']);

        // Gestão de Usuários
        Route::get('/users', [App\Http\Controllers\Api\UserController::class, 'index']);
        
        // 1º As rotas fixas vêm PRIMEIRO
        Route::get('/users/map-stats', [App\Http\Controllers\Api\UserController::class, 'mapStats']); 
        
        // 2º As rotas dinâmicas (com {id}) vêm DEPOIS
        Route::get('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'show']);
        Route::put('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'update']);
        Route::delete('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'destroy']);


        
            });


    
});