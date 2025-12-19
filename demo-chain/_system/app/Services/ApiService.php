<?php

namespace App\Services;

/**
 * フロントAPI取得用サービス
 * CMS APIとの通信を管理する（キャッシュ機能付き）
 */
class ApiService
{
    private $baseEndpoint;
    private $timeout;
    private $debugMode;
    private $endpoint;
    private $cacheEnabled;
    private $cacheTtl;
    private $cachePrefix;
    private $headers;
    private $params;

    public function __construct(string $endpoint = '')
    {
        $this->baseEndpoint = BASE_ENDPOINT;
        $this->timeout = 30;
        $this->debugMode = DEBUG_MODE;
        $this->endpoint = $endpoint;
        $this->cacheEnabled = false;
        $this->cacheTtl = 3600;
        $this->cachePrefix = 'api_cache_';
        $this->headers = [];
        $this->params = [];

        // 設定ファイルからキャッシュ設定を読み込み
        $this->loadCacheConfig();
    }

    /**
     * エンドポイントを設定
     *
     * @param string $endpoint
     * @return self
     */
    public function endpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * キャッシュを有効化
     *
     * @param int|null $ttl キャッシュ時間（秒）
     * @return self
     */
    public function enableCache(?int $ttl = null): self
    {
        $this->cacheEnabled = true;
        if ($ttl !== null) {
            $this->cacheTtl = $ttl;
        }
        return $this;
    }

    /**
     * キャッシュを無効化
     *
     * @return self
     */
    public function disableCache(): self
    {
        $this->cacheEnabled = false;
        return $this;
    }

    /**
     * タイムアウトを設定
     *
     * @param int $timeout
     * @return self
     */
    public function timeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * ヘッダーを追加
     *
     * @param array $headers 連想配列形式（['Header-Name' => 'value']）または文字列配列形式（['Header-Name: value']）
     * @return self
     */
    public function headers(array $headers): self
    {
        // 連想配列形式の場合は文字列形式に変換
        $formattedHeaders = [];
        foreach ($headers as $key => $value) {
            if (is_int($key)) {
                // 既に文字列形式の場合
                $formattedHeaders[] = $value;
            } else {
                // 連想配列形式の場合
                $formattedHeaders[] = $key . ': ' . $value;
            }
        }
        $this->headers = array_merge($this->headers, $formattedHeaders);
        return $this;
    }

    /**
     * トークンを設定
     */
    public function setToken(string $token): self
    {
        $this->headers(['X-CMS-API-KEY' => $token]);
        return $this;
    }

    public function addFacility(string $facilityAlias): self
    {
        $criteria = $this->params['criteria'] ?? [];
        $criteria['facility_alias'] = (isset($criteria['facility_alias'])) ? $criteria['facility_alias'] . ',' . $facilityAlias : $facilityAlias;
        $this->params(['criteria' => $criteria]);

        return $this;
    }

    /**
     * パラメータを追加
     *
     * @param array $params
     * @return self
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * ベースエンドポイントを設定
     *
     * @param string $endpoint
     * @return self
     */
    public function baseEndpoint(string $endpoint): self
    {
        $this->baseEndpoint = $endpoint;
        return $this;
    }

    /**
     * GETリクエストを送信（キャッシュ機能付き）
     *
     * @param string|null $endpoint APIエンドポイント（省略時はコンストラクタで設定したエンドポイントを使用）
     * @param array $params クエリパラメータ
     * @param array $headers 追加ヘッダー
     * @return array|null レスポンスデータ
     */
    public function get(?string $endpoint = null, array $params = [], array $headers = []): ?array
    {
        $targetEndpoint = $endpoint ?? $this->endpoint;
        if (empty($targetEndpoint)) {
            $this->logError("Endpoint not specified");
            return null;
        }

        $mergedParams = array_merge($this->params, $params);
        $mergedHeaders = array_merge($this->headers, $headers);

        // キャッシュが有効な場合はキャッシュから取得を試行
        if ($this->cacheEnabled) {
            $cacheKey = $this->generateCacheKey($targetEndpoint, $mergedParams);
            $cachedData = $this->getFromCache($cacheKey);

            if ($cachedData !== null) {
                return $cachedData;
            }
        }

        $url = $this->buildUrl($targetEndpoint, $mergedParams);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $this->buildHeaders($mergedHeaders),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logError("cURL Error: " . $error . " for URL: " . $url);
            return null;
        }

        if ($httpCode !== 200) {
            $this->logError("HTTP Error: " . $httpCode . " for URL: " . $url . " Response: " . substr($response, 0, 500));
            return null;
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        // キャッシュが有効な場合はデータをキャッシュに保存
        if ($this->cacheEnabled && $data !== null) {
            $this->setToCache($cacheKey, $data, $this->cacheTtl, $targetEndpoint);
        }

        return $data;
    }

    /**
     * POSTリクエストを送信
     *
     * @param string|null $endpoint APIエンドポイント（省略時はコンストラクタで設定したエンドポイントを使用）
     * @param array $data 送信データ
     * @param array $headers 追加ヘッダー
     * @return array|null レスポンスデータ
     */
    public function post(?string $endpoint = null, array $data = [], array $headers = []): ?array
    {
        $targetEndpoint = $endpoint ?? $this->endpoint;
        if (empty($targetEndpoint)) {
            $this->logError("Endpoint not specified");
            return null;
        }

        $mergedHeaders = array_merge($this->headers, $headers);
        $url = $this->buildUrl($targetEndpoint);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $this->buildHeaders($mergedHeaders, 'application/json'),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logError("cURL Error: " . $error . " for URL: " . $url);
            return null;
        }

        if ($httpCode !== 200) {
            $this->logError("HTTP Error: " . $httpCode . " for URL: " . $url . " Response: " . substr($response, 0, 500));
            return null;
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        return $data;
    }

    /**
     * 複数のGETリクエストを非同期で並列実行
     *
     * @param array $requests リクエスト配列
     *   - ApiServiceインスタンス: ['key' => new ApiService('endpoint')->params([...])]
     *   - 連想配列（後方互換性）: ['key' => ['endpoint' => '...', 'params' => [...], 'headers' => [...]]]
     * @return array レスポンス配列（リクエストの順序に対応）['key' => array|null, ...]
     */
    public function getMultiple(array $requests): array
    {
        if (empty($requests)) {
            return [];
        }

        $multiHandle = curl_multi_init();
        $curlHandles = [];
        $requestKeys = [];
        $responses = [];

        // 各リクエストのキャッシュチェックとcURLハンドルの準備
        foreach ($requests as $key => $request) {
            // ApiServiceインスタンスの場合
            if ($request instanceof self) {
                $serviceInstance = $request;
                $endpoint = $serviceInstance->getCurrentEndpoint();
                $params = $serviceInstance->getParams();
                $headers = $serviceInstance->getHeaders();
                $cacheEnabled = $serviceInstance->isCacheEnabled();
                $cacheTtl = $serviceInstance->getCacheTtl();
                $timeout = $serviceInstance->getTimeout();
                $baseEndpoint = $serviceInstance->getBaseEndpoint();
            }
            // 連想配列の場合（後方互換性）
            else {
                $endpoint = $request['endpoint'] ?? $this->endpoint;

                // paramsのマージ（criteria配列を適切にマージ）
                $requestParams = $request['params'] ?? [];
                $params = $this->params;

                // criteria配列がある場合は再帰的にマージ
                if (isset($requestParams['criteria']) && isset($params['criteria'])) {
                    $mergedCriteria = array_merge($params['criteria'], $requestParams['criteria']);
                    $requestParams['criteria'] = $mergedCriteria;
                }
                $params = array_merge($params, $requestParams);

                // headersのマージ
                $headers = array_merge($this->headers, $request['headers'] ?? []);

                $cacheEnabled = $this->cacheEnabled;
                $cacheTtl = $this->cacheTtl;
                $timeout = $this->timeout;
                $baseEndpoint = $this->baseEndpoint;
            }

            // キャッシュが有効な場合はキャッシュから取得を試行
            if ($cacheEnabled) {
                $cacheKey = $this->generateCacheKey($endpoint, $params);
                $cachedData = $this->getFromCache($cacheKey);

                if ($cachedData !== null) {
                    $responses[$key] = $cachedData;
                    continue;
                }
            }

            $url = $this->buildUrl($endpoint, $params, $baseEndpoint);
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_HTTPHEADER => $this->buildHeaders($headers),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
            ]);

            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[$key] = $ch;
            $requestKeys[$key] = [
                'endpoint' => $endpoint,
                'params' => $params,
                'url' => $url,
                'cacheEnabled' => $cacheEnabled,
                'cacheTtl' => $cacheTtl,
            ];
        }

        // すべてのリクエストが完了するまで実行
        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);

        // 各リクエストのレスポンスを取得
        foreach ($curlHandles as $key => $ch) {
            $response = curl_multi_getcontent($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            if ($error) {
                $this->logError("cURL Error: " . $error . " for URL: " . $requestKeys[$key]['url']);
                $responses[$key] = null;
            } elseif ($httpCode !== 200) {
                $this->logError("HTTP Error: " . $httpCode . " for URL: " . $requestKeys[$key]['url'] . " Response: " . substr($response, 0, 500));
                $responses[$key] = null;
            } else {
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->logError("JSON Decode Error: " . json_last_error_msg());
                    $responses[$key] = null;
                } else {
                    // キャッシュが有効な場合はデータをキャッシュに保存
                    if ($requestKeys[$key]['cacheEnabled'] && $data !== null) {
                        $cacheKey = $this->generateCacheKey($requestKeys[$key]['endpoint'], $requestKeys[$key]['params']);
                        $this->setToCache($cacheKey, $data, $requestKeys[$key]['cacheTtl'], $requestKeys[$key]['endpoint']);
                    }
                    $responses[$key] = $data;
                }
            }

            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        return $responses;
    }

    /**
     * 一覧を取得（汎用メソッド、キャッシュ機能付き）
     *
     * @param array $params クエリパラメータ
     * @return array|null
     */
    public function getList(array $params = []): ?array
    {
        $defaultParams = [
            'limit' => 10,
            'offset' => 0,
        ];

        $mergedParams = array_merge($defaultParams, $this->params, $params);
        return $this->get(null, $mergedParams);
    }

    /**
     * 詳細を取得（汎用メソッド、キャッシュ機能付き）
     *
     * @param int $id ID
     * @return array|null
     */
    public function getDetail(int $id): ?array
    {
        return $this->get("{$this->endpoint}/{$id}");
    }

    /**
     * カテゴリ一覧を取得（汎用メソッド、キャッシュ機能付き）
     *
     * @param array $params クエリパラメータ
     * @return array|null
     */
    public function getCategories(array $params = []): ?array
    {
        $defaultParams = [
            'limit' => 50,
            'offset' => 0,
        ];

        $mergedParams = array_merge($defaultParams, $this->params, $params);
        return $this->get("{$this->endpoint}/categories", $mergedParams);
    }

    /**
     * ページネーション情報を取得
     *
     * @param array $response APIレスポンス
     * @return array|null
     */
    public function getPaginationInfo(array $response): ?array
    {
        if (isset($response['pagination'])) {
            return $response['pagination'];
        }

        // paginationキーがない場合は、レスポンス全体から抽出
        $paginationKeys = ['total', 'current', 'pages', 'limit'];
        $pagination = [];

        foreach ($paginationKeys as $key) {
            if (isset($response[$key])) {
                $pagination[$key] = $response[$key];
            }
        }

        return !empty($pagination) ? $pagination : null;
    }

    /**
     * キャッシュをクリア
     *
     * @param string|null $pattern パターン（nullの場合は全てクリア）
     * @return self
     */
    public function clearCache(?string $pattern = null): self
    {
        if (!$this->cacheEnabled) {
            return $this;
        }

        $cacheDir = sys_get_temp_dir() . '/api_cache/';
        if (!is_dir($cacheDir)) {
            return $this;
        }

        $files = glob($cacheDir . $this->cachePrefix . ($pattern ?? '*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return $this;
    }

    /**
     * ニュース一覧を取得
     *
     * @param int $limit 取得件数
     * @param int $offset オフセット
     * @return array|null
     */
    public function getNewsList(int $limit = 10, int $offset = 0): ?array
    {
        $params = [
            'limit' => $limit,
            'offset' => $offset,
            'status' => 'published'
        ];

        return $this->get('news', $params);
    }

    /**
     * ニュース詳細を取得
     *
     * @param int $id ニュースID
     * @return array|null
     */
    public function getNewsDetail(int $id): ?array
    {
        return $this->get("news/{$id}");
    }

    /**
     * コンテンツ一覧を取得
     *
     * @param string $contentType コンテンツタイプ
     * @param int $limit 取得件数
     * @param int $offset オフセット
     * @return array|null
     */
    public function getContentList(string $contentType, int $limit = 10, int $offset = 0): ?array
    {
        $params = [
            'limit' => $limit,
            'offset' => $offset,
            'status' => 'published'
        ];

        return $this->get("content/{$contentType}", $params);
    }

    /**
     * コンテンツ詳細を取得
     *
     * @param string $contentType コンテンツタイプ
     * @param int $id コンテンツID
     * @return array|null
     */
    public function getContentDetail(string $contentType, int $id): ?array
    {
        return $this->get("content/{$contentType}/{$id}");
    }

    /**
     * メディアファイル情報を取得
     *
     * @param int $id メディアID
     * @return array|null
     */
    public function getMedia(int $id): ?array
    {
        return $this->get("media/{$id}");
    }

    /**
     * サイト設定を取得
     *
     * @return array|null
     */
    public function getSiteSettings(): ?array
    {
        return $this->get('settings');
    }

    /**
     * マークアップ一覧を取得（レンダリング済み）
     *
     * @param string $modelName モデル名
     * @param array $params クエリパラメータ
     * @return array|null
     */
    public function getMarkupList(string $modelName, array $params = []): ?array
    {
        $defaultParams = [
            'limit' => 10,
            'offset' => 0,
        ];

        $mergedParams = array_merge($defaultParams, $this->params, $params);
        return $this->get("{$modelName}/markup", $mergedParams);
    }

    /**
     * マークアップ詳細を取得（レンダリング済み）
     *
     * @param string $modelName モデル名
     * @param int $id コンテンツID
     * @param array $params クエリパラメータ
     * @return array|null
     */
    public function getMarkupDetail(string $modelName, int $id, array $params = []): ?array
    {
        $mergedParams = array_merge($this->params, $params);
        return $this->get("{$modelName}/markup/{$id}", $mergedParams);
    }

    /**
     * URLを構築
     *
     * @param string $endpoint
     * @param array $params
     * @param string|null $baseEndpoint ベースエンドポイント（省略時は$this->baseEndpointを使用）
     * @return string
     */
    private function buildUrl(string $endpoint, array $params = [], ?string $baseEndpoint = null): string
    {
        $base = $baseEndpoint ?? $this->baseEndpoint;
        $url = rtrim($base, '/') . '/' . ltrim($endpoint, '/');

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * ヘッダーを構築
     *
     * @param array $additionalHeaders
     * @param string $contentType
     * @return array
     */
    private function buildHeaders(array $additionalHeaders = [], string $contentType = 'application/json'): array
    {
        $headers = [
            'Content-Type: ' . $contentType,
            'Accept: application/json',
            'User-Agent: CMS-Front/1.0'
        ];

        return array_merge($headers, $additionalHeaders);
    }

    /**
     * エラーログを記録
     *
     * @param string $message
     */
    private function logError(string $message): void
    {
        if ($this->debugMode) {
            error_log("[ApiService] " . $message);
            // デバッグモード時はコンソールにも出力
            echo "[ApiService Debug] " . $message . "\n";
        }
    }

    /**
     * キャッシュが有効かどうかを取得
     *
     * @return bool
     */
    public function isCacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    /**
     * 現在のエンドポイントを取得
     *
     * @return string
     */
    public function getCurrentEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * 現在のパラメータを取得
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * 現在のヘッダーを取得
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * ベースエンドポイントを取得
     *
     * @return string
     */
    public function getBaseEndpoint(): string
    {
        return $this->baseEndpoint;
    }

    /**
     * タイムアウトを取得
     *
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * キャッシュTTLを取得
     *
     * @return int
     */
    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    /**
     * 設定ファイルからキャッシュ設定を読み込み
     */
    private function loadCacheConfig(): void
    {
        $configFile = __DIR__ . '/../config/api.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            $this->cachePrefix = $config['cache']['prefix'] ?? 'api_cache_';
        }
    }

    /**
     * キャッシュキーを生成
     *
     * @param string $endpoint
     * @param array $params
     * @return string
     */
    private function generateCacheKey(string $endpoint, array $params = []): string
    {
        $key = $endpoint;
        if (!empty($params)) {
            $key .= '_' . md5(serialize($params));
        }
        return $this->cachePrefix . md5($key);
    }

    /**
     * キャッシュからデータを取得
     *
     * @param string $key
     * @return array|null
     */
    private function getFromCache(string $key): ?array
    {
        $cacheFile = $this->getCacheFilePath($key);

        if (!file_exists($cacheFile)) {
            return null;
        }

        $cacheData = unserialize(file_get_contents($cacheFile));

        if ($cacheData === false || $cacheData['expires'] < time()) {
            unlink($cacheFile);
            return null;
        }

        return $cacheData['data'];
    }

    /**
     * キャッシュにデータを保存
     * 有効期限は次の分の00秒に固定される
     *
     * @param string $key
     * @param array $data
     * @param int $ttl
     * @param string|null $endpoint エンドポイント名（キャッシュクリア用）
     */
    private function setToCache(string $key, array $data, int $ttl, ?string $endpoint = null): void
    {
        $cacheDir = sys_get_temp_dir() . '/api_cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cacheFile = $this->getCacheFilePath($key);

        // 次の分の00秒を計算
        $currentTime = time();
        $nextMinute = strtotime(date('Y-m-d H:i:00', $currentTime + $ttl));

        $cacheData = [
            'data' => $data,
            'expires' => $nextMinute,
            'created' => $currentTime,
            'endpoint' => $endpoint // エンドポイント名を保存（キャッシュクリア用）
        ];

        file_put_contents($cacheFile, serialize($cacheData));
    }

    /**
     * キャッシュファイルパスを取得
     *
     * @param string $key
     * @return string
     */
    private function getCacheFilePath(string $key): string
    {
        $cacheDir = sys_get_temp_dir() . '/api_cache/';
        return $cacheDir . $key . '.cache';
    }

    /**
     * キャッシュディレクトリを取得
     *
     * @return string
     */
    private static function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/api_cache/';
    }

    /**
     * キャッシュプレフィックスを取得
     *
     * @return string
     */
    private static function getCachePrefix(): string
    {
        $configFile = __DIR__ . '/../config/api.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            return $config['cache']['prefix'] ?? 'api_cache_';
        }
        return 'api_cache_';
    }

    /**
     * すべてのキャッシュをクリア（静的メソッド）
     *
     * @return bool
     */
    public static function clearAllCache(): bool
    {
        $cacheDir = self::getCacheDir();
        if (!is_dir($cacheDir)) {
            return true;
        }

        $files = glob($cacheDir . '*');
        $success = true;
        foreach ($files as $file) {
            if (is_file($file)) {
                if (!unlink($file)) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * パターンに一致するキャッシュをクリア（静的メソッド）
     *
     * @param string $pattern パターン（例: 'news_*', '*_123'）
     * @return int 削除されたファイル数
     */
    public static function clearCacheByPattern(string $pattern): int
    {
        $cacheDir = self::getCacheDir();
        if (!is_dir($cacheDir)) {
            return 0;
        }

        $prefix = self::getCachePrefix();
        $pattern = str_replace('*', '.*', $pattern);
        $fullPattern = '/' . preg_quote($prefix, '/') . $pattern . '/';

        $files = glob($cacheDir . $prefix . '*');
        $count = 0;
        foreach ($files as $file) {
            $filename = basename($file, '.cache');
            if (preg_match($fullPattern, $filename)) {
                if (is_file($file) && unlink($file)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * エンドポイントに基づいてキャッシュをクリア（静的メソッド）
     *
     * @param string $endpoint エンドポイント（例: 'news', 'emergency'）
     * @return int 削除されたファイル数
     */
    public static function clearCacheByEndpoint(string $endpoint): int
    {
        $cacheDir = self::getCacheDir();
        if (!is_dir($cacheDir)) {
            return 0;
        }

        $prefix = self::getCachePrefix();
        $files = glob($cacheDir . $prefix . '*');
        $count = 0;

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            // キャッシュファイルの内容を読み込んで、エンドポイント名を確認
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $cacheData = @unserialize($content);
            if ($cacheData === false || !is_array($cacheData)) {
                continue;
            }

            // キャッシュデータにエンドポイント名が保存されているか確認
            if (isset($cacheData['endpoint']) && $cacheData['endpoint'] === $endpoint) {
                if (unlink($file)) {
                    $count++;
                }
                continue;
            }

            // エンドポイント名が保存されていない古いキャッシュファイルの場合
            // キャッシュキーから推測を試行
            $filename = basename($file, '.cache');
            $baseKey = $endpoint;
            $baseKeyHash = md5($baseKey);
            $baseCacheKey = $prefix . md5($baseKey);

            // エンドポイントのみのキャッシュキーと一致するか
            if ($filename === $baseCacheKey) {
                if (unlink($file)) {
                    $count++;
                }
                continue;
            }

            // パラメータがある場合のキャッシュキーをチェック
            // エンドポイント名のハッシュが含まれるファイルを削除
            // 注意: これは完全に正確ではない可能性があるが、古いキャッシュファイル用のフォールバック
            if (strpos($filename, $baseKeyHash) !== false) {
                if (unlink($file)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * モデル名に基づいてキャッシュをクリア（静的メソッド）
     * モデル名はエンドポイント名として扱われる
     *
     * @param string $modelName モデル名（例: 'news', 'emergency'）
     * @return int 削除されたファイル数
     */
    public static function clearCacheByModel(string $modelName): int
    {
        // モデル名はエンドポイント名として扱う
        return self::clearCacheByEndpoint($modelName);
    }
}
