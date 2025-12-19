<?php

/**
 * API設定ファイル
 * フロントAPI取得用の設定を管理
 */

return [
    // API基本設定
    'api' => [
        'base_endpoint' => BASE_ENDPOINT,
        'timeout' => 30,
        'retry_count' => 3,
        'retry_delay' => 1000, // ミリ秒
    ],

    // キャッシュ設定
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1時間
        'prefix' => 'api_cache_',
    ],

    // ログ設定
    'logging' => [
        'enabled' => DEBUG_MODE,
        'level' => LOG_LEVEL,
        'file' => '/tmp/api_service.log',
    ],

    // デフォルトパラメータ
    'defaults' => [
        'limit' => 10,
        'offset' => 0,
        'status' => 'published',
        'order_by' => 'created_at',
        'order_direction' => 'desc',
    ],

    // エラーハンドリング
    'error_handling' => [
        'fallback_data' => [
            'news' => [],
            'content' => [],
            'media' => null,
            'settings' => [],
        ],
        'show_errors' => DEBUG_MODE,
    ],

    // WebHook設定
    'webhook' => [
        'token' => $_ENV['WEBHOOK_TOKEN'] ?? getenv('WEBHOOK_TOKEN') ?? null,
        'enabled' => true,
    ],
];
