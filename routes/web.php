<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

Route::view('/', 'app');

Route::view('/login', 'app')->name('login');

Route::prefix('api')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('posts', PostController::class);
    });
});

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');