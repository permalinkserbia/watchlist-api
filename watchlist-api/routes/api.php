<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WatchlistController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'watchlist'], function () {
        Route::get('/', [WatchlistController::class, 'getWatchlist']);
        Route::post('/', [WatchlistController::class, 'addMovieToWatchlist']);
        Route::put('/{id}', [WatchlistController::class, 'updateWatchlistItem']);
        Route::delete('/{id}', [WatchlistController::class, 'removeMovieFromWatchlist']);
    });
});
