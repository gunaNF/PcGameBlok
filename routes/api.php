<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameLibraryController;
use App\Http\Controllers\Api\GenreController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('genres', GenreController::class);
    Route::apiResource('game-libraries', GameLibraryController::class);
});