<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ALBヘルスチェック用
Route::get('/healthz', function () {
    return response()->json(['status' => 'ok'], 200);
})->withoutMiddleware(['web']);

// Sanctum CSRF Cookie エンドポイント（SPA認証用）
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set'], 200);
})->middleware('web');
