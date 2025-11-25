<?php

// Rutas públicas - Autenticación de Usuario Final

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PremiosController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Rutas protegidas - Catálogo de Premios (requiere session del SOAP)
Route::middleware('auth.session')->group(function () {
    Route::get('/premios', [PremiosController::class, 'index'])->name('api.premios.index');
});

