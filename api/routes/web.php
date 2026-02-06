<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ALBヘルスチェック用
Route::get('/healthz', function () {
    return response()->json(['status' => 'ok'], 200);
})->withoutMiddleware(['web']);

// Sanctum CSRF Cookie（SPA認証用）。フロントは /api/sanctum/csrf-cookie を呼ぶため両方対応
$csrfCookie = function (\Illuminate\Http\Request $request) {
    if (!$request->hasSession()) {
        $request->session()->start();
    }
    $token = $request->session()->token();
    return response()->json(['message' => 'CSRF cookie set'], 200)
        ->cookie('XSRF-TOKEN', $token, 0, '/', null, true, false, false, 'None');
};
Route::get('/sanctum/csrf-cookie', $csrfCookie)->middleware('web');
Route::get('/api/sanctum/csrf-cookie', $csrfCookie)->middleware('web');
