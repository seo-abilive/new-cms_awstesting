<?php

return [
    'list' => [
        'limit' => 10
    ],
    'saving' => [
        'update_free_search' => true,
        'sort_num' => false,
        'status' => false,
        'publish_period' => false,
    ],
    'login' => [
        // ログイン試行回数の上限
        'max_attempts' => env('LOGIN_MAX_ATTEMPTS', 5),
        // ロック時間（分）
        'lockout_duration' => env('LOGIN_LOCKOUT_DURATION', 15),
    ],
];
