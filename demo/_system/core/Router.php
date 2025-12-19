<?php

namespace Core;

/**
 * Router class
 */
class Router
{
    private array $routes = [];

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
            } elseif ($path[0] !== '/') {
                // ベースパス除去後に先頭のスラッシュがなくなった場合は追加
                $path = '/' . $path;
            }
        }

        if (empty($this->routes[$method])) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        // パスを正規化（末尾のスラッシュを統一）
        $normalizedPath = rtrim($path, '/');
        if (empty($normalizedPath)) {
            $normalizedPath = '/';
        }

        // まず完全一致のルートをチェック（動的パラメータを含まないルート）
        foreach ($this->routes[$method] as $route => $callback) {
            // 動的パラメータ（:idなど）を含まないルートを優先的にチェック
            if (strpos($route, ':') === false) {
                // ルートも正規化
                $normalizedRoute = rtrim($route, '/');
                if (empty($normalizedRoute)) {
                    $normalizedRoute = '/';
                }

                // 完全一致をチェック（パスとルートの両方を正規化して比較）
                if ($normalizedRoute === $normalizedPath || $route === $path) {
                    // 完全一致
                    if (is_array($callback)) {
                        $controller = new $callback[0]();
                        $controllerMethod = $callback[1];
                        call_user_func_array([$controller, $controllerMethod], []);
                    } else {
                        call_user_func($callback);
                    }
                    return;
                }
            }
        }

        // 完全一致がない場合、動的パラメータを含むルートをチェック
        foreach ($this->routes[$method] as $route => $callback) {
            // 動的パラメータを正規表現に変換
            $pattern = preg_replace('#:([\w]+)#', '([^/]+)', $route);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $normalizedPath, $matches)) {
                array_shift($matches); // $matches[0] はフルマッチ

                // 配列形式のコールバックを処理
                if (is_array($callback)) {
                    $controller = new $callback[0]();
                    $controllerMethod = $callback[1];
                    call_user_func_array([$controller, $controllerMethod], $matches);
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
            // _systemより上の階層を取得
            $basePath = dirname(dirname($scriptName));

            // 開発環境でベースパスがルート（/）の場合は、ベースパスを空にする
            if ($basePath === '/' && strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false) {
                $basePath = '';
            }

            return $basePath;
        }

        // フォールバック：ドキュメントルートからの相対パス
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $currentDir = dirname($_SERVER['SCRIPT_FILENAME'] ?? '');

        if ($documentRoot && $currentDir) {
            return str_replace($documentRoot, '', $currentDir);
        }

        return '';
    }
}
