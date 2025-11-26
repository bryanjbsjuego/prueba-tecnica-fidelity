<?php

// Rutas públicas - Autenticación de Usuario Final

use App\Http\Controllers\Api\AllianceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AwardController;
use App\Http\Controllers\Api\OperatorAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Rutas protegidas - Catálogo de Premios (requiere session del SOAP)
Route::middleware('auth.session')->group(function () {
    Route::get('/premios', [AwardController::class, 'index'])->name('api.premios.index');
});


Route::prefix('operator')->group(function () {

    // Login de operador
    Route::post('/login', [OperatorAuthController::class, 'login'])
        ->name('operator.login');

    // Rutas protegidas con JWT
    Route::middleware(['auth.jwt'])->group(function () {

        // Logout
        Route::post('/logout', [OperatorAuthController::class, 'logout'])
            ->name('operator.logout');

        // Alianzas
        Route::get('/alianzas', [AllianceController::class, 'index'])
            ->name('alianzas.index');

        Route::get('/alianzas/{id}', [AllianceController::class, 'show'])
            ->name('alianzas.show');

        Route::post('/alianzas/marcar-usada', [AllianceController::class, 'used'])
            ->name('alianzas.marcar-usada');
    });
});

// Ruta de health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Conecta',
        'version' => '1.0.0',
        'timestamp' => now(),
    ]);
})->name('api.health');
