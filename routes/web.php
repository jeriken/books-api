<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ping', function () {
    return response()->json(['success' => true], 200);
});

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
