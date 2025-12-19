<?php

namespace Core;

/**
 * Router class
 */
class Router
{
    private static ?Router $instance = null;
    private array $routes = [];
    private array $currentParams = [];
    private ?string $currentRoute = null;

    /**
     * シングルトンインスタンスを取得
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * プライベートコンストラクタ（シングルトンパターン）
     */
    private function __construct() {}

    /**
     * クローンを防ぐ
     */
    private function __clone() {}

    /**
     * アンシリアライズを防ぐ
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    public function get(string $path, $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // ベースパスを取得（サブディレクトリ対応）
        $basePath = $this->getBasePath();

        // ベースパスを除去
        if ($basePath && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
            // パスが空になった場合はルート（/）として扱う
            if (empty($path)) {
                $path = '/';
            }
        }

        // パスの正規化：末尾スラッシュを削除（ルート（/）以外）
        $normalizedPath = $path;
        if ($path !== '/' && substr($path, -1) === '/') {
            $normalizedPath = rtrim($path, '/');
        }

        if (empty($this->routes[$method])) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        foreach ($this->routes[$method] as $route => $callback) {
            // ルート定義も正規化（末尾スラッシュを削除、ルート（/）以外）
            $normalizedRoute = $route;
            if ($route !== '/' && substr($route, -1) === '/') {
                $normalizedRoute = rtrim($route, '/');
            }

            // 動的パラメータを正規表現に変換
            $pattern = preg_replace('#:([\w-]+)#', '([^/]+)', $normalizedRoute);
            $pattern = "#^{$pattern}$#";

            // 正規化したパスでマッチング
            if (preg_match($pattern, $normalizedPath, $matches)) {
                array_shift($matches); // $matches[0] はフルマッチ

                // ルートパラメータを保存
                $routeParams = $this->extractRouteParams($normalizedRoute);
                $this->currentRoute = $normalizedRoute;
                $this->currentParams = [];
                foreach ($routeParams as $index => $paramName) {
                    if (isset($matches[$index])) {
                        $this->currentParams[$paramName] = $matches[$index];
                    }
                }

                // 配列形式のコールバックを処理
                if (is_array($callback)) {
                    $controller = new $callback[0]();
                    $method = $callback[1];
                    call_user_func_array([$controller, $method], $matches);
                } else {
                    call_user_func_array($callback, $matches);
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    /**
     * ベースパスを取得
     */
    private function getBasePath(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        // index.phpのパスからベースパスを取得
        if (strpos($scriptName, '/_system/index.php') !== false) {
            // _system/index.phpを除去してベースパスを取得
            $basePath = str_replace('/_system/index.php', '', $scriptName);

            // ベースパスが空の場合はルート（/）として扱う
            if (empty($basePath)) {
                $basePath = '';
            }

            return $basePath;
        }

        // フォールバック：ドキュメントルートからの相対パス
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $currentDir = dirname($_SERVER['SCRIPT_FILENAME'] ?? '');

        if ($documentRoot && $currentDir) {
            $basePath = str_replace($documentRoot, '', $currentDir);
            // _systemディレクトリを除去
            $basePath = str_replace('/_system', '', $basePath);
            return $basePath;
        }

        return '';
    }

    /**
     * ルート定義からパラメータ名を抽出
     */
    private function extractRouteParams(string $route): array
    {
        $params = [];
        if (preg_match_all('#:([\w-]+)#', $route, $matches)) {
            $params = $matches[1];
        }
        return $params;
    }

    /**
     * 現在のルートパラメータを取得
     */
    public function getParams(): array
    {
        return $this->currentParams;
    }

    /**
     * 指定されたパラメータ名の値を取得
     */
    public function getParam(string $name, $default = null)
    {
        return $this->currentParams[$name] ?? $default;
    }

    /**
     * 現在のルートを取得
     */
    public function getCurrentRoute(): ?string
    {
        return $this->currentRoute;
    }
}
