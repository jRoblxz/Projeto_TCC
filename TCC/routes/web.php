<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index'])->name('users.index'); #tela de visualização de usuários
Route::get('/form1', [UserController::class, 'create'])->name('users.create'); #tela do formulário de criação de usuário
Route::post('/form1', [UserController::class, 'store'])->name('users.store'); #rota para salvar o novo usuário

Route::get('/', function () {
    return view('frontend.home');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/form1', function () {
    return view('telas_forms.forms1');
});

Route::get('/index', function () {
    return view('telas_forms.index');
});

Route::get('/dashboard', function () {
    // Dados fake só para teste
    $usuario = "Usuário Teste";
    return view('frontend.dashboard', compact('usuario'));
});