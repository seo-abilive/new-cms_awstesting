<?php

return [
    'list' => [
        'limit' => 10
    ],
    'storage' => [
        /*
         * ファイルアップロード先のストレージディスク
         *
         * 設定可能な値:
         * - 'public': ローカルのpublicストレージに保存（storage/app/public）
         * - 's3': AWS S3またはLocalStackに保存
         *
         * 環境変数: MEDIA_LIBRARY_STORAGE_DISK
         *
         * S3を使用する場合の環境変数設定例（LocalStack使用時）:
         * AWS_ACCESS_KEY_ID=test
         * AWS_SECRET_ACCESS_KEY=test
         * AWS_DEFAULT_REGION=us-east-1
         * AWS_BUCKET=test-bucket
         * AWS_ENDPOINT=http://localstack:4566
         * AWS_USE_PATH_STYLE_ENDPOINT=true
         * AWS_URL=http://localhost:4566/test-bucket
         *
         * 注意: AWS_ENDPOINTはDockerコンテナ内からのアクセス用（localstack）で、
         * AWS_URLはブラウザからのアクセス用（localhost）を設定してください。
         */
        'disk' => env('MEDIA_LIBRARY_STORAGE_DISK', 'public'),
    ],
    'upload' => [
        // 許可する拡張子
        'allowed_extensions' => [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'svg', // 画像
            'pdf', // PDF
            'doc',
            'docx', // Word
            'xls',
            'xlsx', // Excel
            'txt',
            'csv', // テキスト
            'mp4',
            'mov',
            'avi',
            'wmv',
            'webm',
            'mkv', // 動画
        ],
        // 許可するMIMEタイプ
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv',
            'video/webm',
            'video/x-matroska',
        ],
        // 最大ファイルサイズ（バイト単位、デフォルト: 100MB）
        'max_file_size' => env('MEDIA_LIBRARY_MAX_FILE_SIZE', 100 * 1024 * 1024),
    ]
];
