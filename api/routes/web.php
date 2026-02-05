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
// web ミドルウェアが自動的にセッションを開始し、CSRF トークンを生成する
Route::get('/sanctum/csrf-cookie', function (\Illuminate\Http\Request $request) {
    // セッションが開始されていることを確認
    if (!$request->hasSession()) {
        $request->session()->start();
    }
    
    // CSRF トークンを取得（セッションから）
    $token = $request->session()->token();
    
    // XSRF-TOKEN クッキーを明示的に設定（Laravel のデフォルト動作を補強）
    $response = response()->json([
        'message' => 'CSRF cookie set',
    ], 200);
    
    // XSRF-TOKEN クッキーを設定（SameSite=None, Secure で CloudFront 経由でも動作）
    $response->cookie('XSRF-TOKEN', $token, 0, '/', null, true, false, false, 'None');
    
    return $response;
})->middleware('web');
