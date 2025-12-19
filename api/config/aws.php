<?php

return [
    's3' => [
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
        'region' => env('AWS_REGION', 'ap-northeast-1'),
        'version' => 'latest',
        'suppress_php_deprecation_warning' => true,
    ],
    'aws_user_id' => env('AWS_USER_ID', ''),
    'aws_bucket_suffix' => env('AWS_BUCKET_SUFFIX', ''),
];
