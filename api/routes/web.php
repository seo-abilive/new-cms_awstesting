<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ALBヘルスチェック用
Route::get('/healthz', function () {
    return response()->json(['status' => 'ok'], 200);
})->withoutMiddleware(['web']);
