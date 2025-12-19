<?php

/**
 * 環境設定
 * ドメインによって環境を自動判定
 */

// 現在のドメインを取得
$currentDomain = $_SERVER['HTTP_HOST'] ?? 'localhost';

// 環境判定
$environment = 'production'; // デフォルトは本番環境

if (
    strpos($currentDomain, 'localhost') !== false ||
    strpos($currentDomain, '127.0.0.1') !== false ||
    strpos($currentDomain, '.local') !== false ||
    strpos($currentDomain, '.dev') !== false
) {
    $environment = 'development';
} elseif (
    strpos($currentDomain, 'staging') !== false ||
    strpos($currentDomain, 'test') !== false
) {
    $environment = 'staging';
}

// 環境定数を定義
define('ENVIRONMENT', $environment);
define('IS_DEVELOPMENT', $environment === 'development');
define('IS_STAGING', $environment === 'staging');
define('IS_PRODUCTION', $environment === 'production');

// 環境別設定
switch ($environment) {
    case 'development':
        // 開発環境
        define('DEBUG_MODE', true);
        define('BASE_ENDPOINT', 'http://new-cms-api/api/v1/');
        define('LOG_LEVEL', 'debug');
        define('API_NEWS_TOKEN', '3425ca12d78e8ecf9e49942f9e7aebedfe2bfd4bf2a12a41c056c8d07eb386c2');
        define('API_FAQ_TOKEN', '7cafa5c47d2c81e6e2afbd2e38b4d84044afe262d207c0808822cfdaa915ed27');
        define('API_TOP_BANNER_TOKEN', '0c71bf24489aa806ef9075e296ec6653bd7a8ad385cbf0da282c2909fe7e5bcd');
        define('CONTACT_WIDGET_CODE', '<iframe src="http://localhost:5173/widget/contact/f13cffd6-4023-48af-8ea4-87c4214717d1" style="border: none; width: 100%; height: 600px"></iframe>');
        break;

    case 'staging':
        // ステージング環境
        define('DEBUG_MODE', true);
        define('BASE_ENDPOINT', 'https://abilive:test@abitestxsrv.xbiz.jp/new-cms/api/public/api/v1/');
        define('LOG_LEVEL', 'info');
        define('API_NEWS_TOKEN', '3425ca12d78e8ecf9e49942f9e7aebedfe2bfd4bf2a12a41c056c8d07eb386c2');
        define('API_FAQ_TOKEN', '7cafa5c47d2c81e6e2afbd2e38b4d84044afe262d207c0808822cfdaa915ed27');
        define('API_TOP_BANNER_TOKEN', '0c71bf24489aa806ef9075e296ec6653bd7a8ad385cbf0da282c2909fe7e5bcd');
        define('CONTACT_WIDGET_CODE', '<iframe src="https://abitestxsrv.xbiz.jp/new-cms/console/dist/widget/contact/f13cffd6-4023-48af-8ea4-87c4214717d1" style="border: none; width: 100%; height: 600px"></iframe>');
        break;

    case 'production':
        // 本番環境
        define('DEBUG_MODE', false);
        define('BASE_ENDPOINT', 'https://abilive:test@abitestxsrv.xbiz.jp/new-cms/api/public/api/v1/');
        define('LOG_LEVEL', 'error');
        define('API_NEWS_TOKEN', '3425ca12d78e8ecf9e49942f9e7aebedfe2bfd4bf2a12a41c056c8d07eb386c2');
        define('API_FAQ_TOKEN', '7cafa5c47d2c81e6e2afbd2e38b4d84044afe262d207c0808822cfdaa915ed27');
        define('API_TOP_BANNER_TOKEN', '0c71bf24489aa806ef9075e296ec6653bd7a8ad385cbf0da282c2909fe7e5bcd');
        define('CONTACT_WIDGET_CODE', '<iframe src="https://abitestxsrv.xbiz.jp/new-cms/console/dist/widget/contact/f13cffd6-4023-48af-8ea4-87c4214717d1" style="border: none; width: 100%; height: 600px"></iframe>');
        break;
}

// 共通設定
define('APP_NAME', 'CMS System');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'Asia/Tokyo');

// タイムゾーン設定
date_default_timezone_set(TIMEZONE);

// デバッグモードの設定
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// エスケープ関数
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
