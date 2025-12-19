<?php
// 環境設定を最初に読み込み
require_once __DIR__ . '/app/config/config.php';

require_once __DIR__ . '/vendor/autoload.php';

use Core\Router;

$router = Router::getInstance();

// ルート定義を読み込み
require_once __DIR__ . '/app/routes.php';

$router->dispatch($_SERVER['REQUEST_URI']);
