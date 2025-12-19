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
        define('API_EMERGENCY_TOKEN', '2aa9fbb8567a8ef4ac7fb84437d6a2b6896d5ed6d7425991c9499632556a923e');
        define('API_TOP_BANNER_TOKEN', '1791094e4982f1f403caea5f19f5055432f33b25607351a0f57d5d45f31a6b4d');
        define('API_NEWS_TOKEN', 'f2b8bd847534a349a6adea66806a887daa95df8747d65ae0fee220f60852c0ec');
        define('API_FAQ_TOKEN', '25f0597a165e4b9a6b68d3e29b647c5e4368de9069fd2ed3024a1512b0ed1116');
        define('API_FACILITY_SETTING_TOKEN', 'ea8516db0e6f134539bbc1ea41b1f1b28ed8e6ee9d4077ed9c0b4490c9e44979');
        define('API_TOP_PAGE_SETTING_TOKEN', '526ef3e9e66a4fdff77fe57d2fb7ed2ffeda07abad78739aa325af1713b81634');
        define('API_PAGE_SETTING_TOKEN', '354a37944dfa09e466d0e2a2eba6cfa72261266881aa4ee301c2b4c5635fee79');
        define('API_ROOMS_INFO_TOKEN', '5c5119a1641afbdcd9812f8fedee5eb9e9831624b401c42067df9097c25b9637');
        define('API_ROOMS_TOKEN', 'f05d998ed837ffa20ec8d0c7221769a807b421cdf899d28e5bd915916523caa5');
        define('API_ACCESS_TOKEN', '58340748518568fe4b43b4d0291edd3d0e8eeaed86e9a4749404733891355fec');
        break;

    case 'staging':
        // ステージング環境
        define('DEBUG_MODE', false);
        define('BASE_ENDPOINT', 'https://abilive:test@abitestxsrv.xbiz.jp/new-cms/api/public/api/v1/');
        define('LOG_LEVEL', 'info');
        define('API_EMERGENCY_TOKEN', '15587cae1d54f1ee5e879e3ac0ca9aba652ec0b1f464b29264e2b746111ea520');
        define('API_TOP_BANNER_TOKEN', '49c58fa60220d7860e2b6206918f7d4f8de953a554b84a0462ea24be22ceb3d6');
        define('API_NEWS_TOKEN', 'f2b8bd847534a349a6adea66806a887daa95df8747d65ae0fee220f60852c0ec');
        define('API_FAQ_TOKEN', '2f9638a13539767251e180bbe11ac9505534a7eed7f60a15dbf96d04c2b5bc91');
        define('API_FACILITY_SETTING_TOKEN', 'f56771af7b785e459f20ffc3696a29ce6e3dddd7f88f318b2309a8394b472b58');
        define('API_TOP_PAGE_SETTING_TOKEN', '8f5a8ff2d3bba5783dbe0a7fa737696dd959693015483d21df31bf87e9b9d11b');
        define('API_PAGE_SETTING_TOKEN', '9c67c0c1c1269071d044043c58151f6f123580f8f43a40eb99dc65205574c5d9');
        define('API_ROOMS_INFO_TOKEN', '24c884f4a92ee84690a657662f8a6b0ade6f740fc6ec110fb10755402c1c316f');
        define('API_ROOMS_TOKEN', '9fc8a8f936e09248393e61fa1d3d3b894bc37552bda16ea5f0c92d75027e90cc');
        define('API_ACCESS_TOKEN', '3a06722dae88e6e89440cd97f9acc6c090dbaa832862ac8b3f578199aa4b886b');
        break;

    case 'production':
        // 本番環境
        define('DEBUG_MODE', false);
        define('BASE_ENDPOINT', 'https://abilive:test@abitestxsrv.xbiz.jp/new-cms/api/public/api/v1/');
        define('LOG_LEVEL', 'error');
        define('API_EMERGENCY_TOKEN', '2aa9fbb8567a8ef4ac7fb84437d6a2b6896d5ed6d7425991c9499632556a923e');
        define('API_TOP_BANNER_TOKEN', '1791094e4982f1f403caea5f19f5055432f33b25607351a0f57d5d45f31a6b4d');
        define('API_NEWS_TOKEN', 'f2b8bd847534a349a6adea66806a887daa95df8747d65ae0fee220f60852c0ec');
        define('API_FAQ_TOKEN', '25f0597a165e4b9a6b68d3e29b647c5e4368de9069fd2ed3024a1512b0ed1116');
        define('API_FACILITY_SETTING_TOKEN', 'ea8516db0e6f134539bbc1ea41b1f1b28ed8e6ee9d4077ed9c0b4490c9e44979');
        define('API_TOP_PAGE_SETTING_TOKEN', '526ef3e9e66a4fdff77fe57d2fb7ed2ffeda07abad78739aa325af1713b81634');
        define('API_PAGE_SETTING_TOKEN', '354a37944dfa09e466d0e2a2eba6cfa72261266881aa4ee301c2b4c5635fee79');
        define('API_ROOMS_INFO_TOKEN', '5c5119a1641afbdcd9812f8fedee5eb9e9831624b401c42067df9097c25b9637');
        define('API_ROOMS_TOKEN', 'f05d998ed837ffa20ec8d0c7221769a807b421cdf899d28e5bd915916523caa5');
        define('API_ACCESS_TOKEN', '58340748518568fe4b43b4d0291edd3d0e8eeaed86e9a4749404733891355fec');
        break;
}

// 共通設定
define('APP_NAME', 'CMS System');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'Asia/Tokyo');

// WEBルート
define('WEB_ROOT', realpath(__DIR__ . '/../../'));


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
