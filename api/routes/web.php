<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ALBヘルスチェック用
Route::get('/healthz', function () {
    return response()->json(['status' => 'ok'], 200);
})->withoutMiddleware(['web']);

// Sanctum CSRF Cookie 用ハンドラ（SPA認証用・パス重複を避けるため共通化）
$sanctumCsrfCookieHandler = function (\Illuminate\Http\Request $request) {
    // セッションが開始されていることを確認
    if (!$request->hasSession()) {
        $request->session()->start();
    }
    $token = $request->session()->token();
    $response = response()->json(['message' => 'CSRF cookie set'], 200);
    // XSRF-TOKEN クッキー（SameSite=None, Secure で CloudFront 経由でも動作）
    $response->cookie('XSRF-TOKEN', $token, 0, '/', null, true, false, false, 'None');
    return $response;
};

// Sanctum CSRF Cookie エンドポイント（SPA認証用）
// フロントは VITE_API_ORIGIN + '/sanctum/csrf-cookie' で /api/sanctum/csrf-cookie を呼ぶため両方対応
Route::get('/sanctum/csrf-cookie', $sanctumCsrfCookieHandler)->middleware('web');
Route::get('/api/sanctum/csrf-cookie', $sanctumCsrfCookieHandler)->middleware('web');
