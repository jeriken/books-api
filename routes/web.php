<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Lean routes — no session/cookie overhead
Route::withoutMiddleware([StartSession::class, EncryptCookies::class])->group(function () {
    Route::post('/echo', function (Request $request) {
        return response()->json((object) $request->json()->all(), 200);
    });

    // Auth
    Route::post('/auth/token', [AuthController::class, 'token']);

    // Books CRUD
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);

    // Protected
    Route::middleware('auth.token')->group(function () {
        Route::get('/books', [BookController::class, 'index']);
    });
});
