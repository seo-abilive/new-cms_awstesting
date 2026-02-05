<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'api/admin/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // 資格情報付き(Cookie)リクエストではワイルドカード不可。フロントのオリジンを明示
    // CORS_EXTRA_ORIGINS は Terraform 等で CloudFront URL などを追加（カンマ区切り）
    'allowed_origins' => array_values(array_filter(array_merge(
        [
            'http://localhost:5173',
            'http://localhost:8000',
            'http://new-cms-main-alb-1834578746.ap-northeast-1.elb.amazonaws.com',
        ],
        explode(',', (string) env('CORS_EXTRA_ORIGINS', ''))
    ))),

    // CloudFront など https://*.cloudfront.net を許可
    'allowed_origins_patterns' => ['#^https?://[a-z0-9-]+\\.cloudfront\\.net$#i'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
