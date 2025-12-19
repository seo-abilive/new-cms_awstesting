<?php
// 環境変数の読み込み
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// .envファイルの読み込み
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} else {
    die('ERROR: .envファイルがありません。README.mdを参照して.envファイルを作成してください。');
}

// AWS設定
$awsConfig = [
    'version' => 'latest',
    'region' => $_ENV['AWS_REGION'] ?? 'ap-northeast-2',
    'credentials' => [
        'key' => $_ENV['AWS_ACCESS_KEY_ID'] ?? '',
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '',
    ],
    'suppress_php_deprecation_warning' => true, // PHP 8.0 deprecation警告を抑制
];

// AWS S3クライアントの作成
$s3Client = new Aws\S3\S3Client($awsConfig);

// AWSユーザーID
$awsUserId = $_ENV['AWS_USER_ID'] ?? '';

// バケットサフィックス
$bucketSuffix = $_ENV['AWS_BUCKET_SUFFIX'] ?? '';

// バケットプレフィックス（AWS_USER_ID + '-'）
// IAMポリシーが993704921089-*で始まるバケットのみ許可するため、この形式を使用
$bucketPrefix = '';
if (!empty($awsUserId)) {
    $bucketPrefix = $awsUserId . '-';
} else {
    $bucketPrefix = 'user-bucket-';
}

// ユーザー名検証関数（英数字と特殊記号のみ許可、日本語・中国語・韓国語などは不可）
function validateUsername($username)
{
    // 英数字と特殊記号（_、-、.）のみ許可
    // 日本語、中国語、韓国語などのマルチバイト文字を除外
    return preg_match('/^[a-zA-Z0-9._-]+$/', $username) && strlen($username) > 0 && mb_strlen($username, 'UTF-8') === strlen($username);
}

// S3バケット名生成関数（S3の命名規則に準拠）
// 形式: AWS_USER_ID + '-' + ユーザー名 + '-' + AWS_BUCKET_SUFFIX
function generateBucketName($prefix, $username, $userId = '', $suffix = '')
{
    // ユーザー名を小文字に変換し、S3で許可されない文字を変換
    $sanitized = strtolower($username);
    // アンダースコアとドットをハイフンに変換（S3ではアンダースコアが許可されない）
    $sanitized = str_replace(['_', '.'], '-', $sanitized);
    // 連続するハイフンを1つに統合
    $sanitized = preg_replace('/-+/', '-', $sanitized);
    // 先頭と末尾のハイフンを削除
    $sanitized = trim($sanitized, '-');

    // バケット名を生成: {userId}-{username}-{suffix}
    $bucketName = $prefix . $sanitized;
    if (!empty($suffix)) {
        $bucketName .= '-' . $suffix;
    }

    return $bucketName;
}
