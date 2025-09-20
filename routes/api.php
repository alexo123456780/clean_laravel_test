<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controllers\UsuarioController;






// Usuario resource routes
Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'store']);
    Route::get('/{usuario}', [UsuarioController::class, 'show']);
    Route::put('/{usuario}', [UsuarioController::class, 'update']);
    Route::delete('/{usuario}', [UsuarioController::class, 'destroy']);
});

