<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // HTMLレスポンスにのみCSPヘッダーを設定
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        // 信頼できるソースのみを許可するCSPポリシー
        $csp = [
            // デフォルトソース: 同一オリジンのみ
            "default-src 'self'",
            
            // スクリプトソース: 同一オリジン + 信頼できるCDN
            // 'unsafe-inline'は既存のインラインスクリプト（Google Tag Manager等）のために必要
            // 'unsafe-eval'は一部のライブラリ（例: lazysizes）のために必要
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                "https://cdn.jsdelivr.net " .
                "https://code.jquery.com " .
                "https://cdn.redoc.ly " .
                "https://www.googletagmanager.com " .
                "https://control.abi-chat.net",
            
            // スタイルソース: 同一オリジン + 信頼できるCDN + インラインスタイル
            "style-src 'self' 'unsafe-inline' " .
                "https://cdn.jsdelivr.net " .
                "https://fonts.googleapis.com " .
                "https://use.typekit.net",
            
            // フォントソース: 同一オリジン + 信頼できるCDN + data URI
            "font-src 'self' " .
                "https://fonts.gstatic.com " .
                "https://use.typekit.net " .
                "data:",
            
            // 画像ソース: 同一オリジン + すべてのHTTPS + data URI + blob URI
            "img-src 'self' data: https: blob:",
            
            // 接続ソース: 同一オリジン + 信頼できるAPI
            "connect-src 'self' " .
                "https://www.googletagmanager.com " .
                "https://control.abi-chat.net",
            
            // フレームソース: 同一オリジン + Google Tag Manager
            "frame-src 'self' " .
                "https://www.googletagmanager.com",
            
            // オブジェクトソース: なし（Flash等を禁止）
            "object-src 'none'",
            
            // ベースURI: 同一オリジンのみ
            "base-uri 'self'",
            
            // フォームアクション: 同一オリジンのみ
            "form-action 'self'",
            
            // フレーム祖先: なし（クリックジャッキング対策）
            "frame-ancestors 'none'",
            
            // 安全でないHTTPリクエストをHTTPSにアップグレード
            "upgrade-insecure-requests",
        ];

        $cspHeader = implode('; ', $csp);
        $response->headers->set('Content-Security-Policy', $cspHeader);

        return $response;
    }
}

