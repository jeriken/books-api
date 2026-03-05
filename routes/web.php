<?php

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
