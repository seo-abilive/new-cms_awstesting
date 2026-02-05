<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// 連続スラッシュを1つに正規化（例: /api//sanctum/csrf-cookie → /api/sanctum/csrf-cookie）
// REQUEST_URI と PATH_INFO の両方を正規化（Laravel が両方を使用する可能性があるため）
if (isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = preg_replace('#/+#', '/', $_SERVER['REQUEST_URI']);
}
if (isset($_SERVER['PATH_INFO'])) {
    $_SERVER['PATH_INFO'] = preg_replace('#/+#', '/', $_SERVER['PATH_INFO']);
}
// QUERY_STRING が含まれている場合も考慮
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '?') !== false) {
    list($path, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
    $_SERVER['REQUEST_URI'] = preg_replace('#/+#', '/', $path) . '?' . $query;
}

$app->handleRequest(Request::capture());
