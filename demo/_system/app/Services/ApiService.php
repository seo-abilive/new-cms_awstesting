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
            $this->setToCache($cacheKey, $data, $this->cacheTtl);
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
     * @return string
     */
    private function buildUrl(string $endpoint, array $params = []): string
    {
        $url = rtrim($this->baseEndpoint, '/') . '/' . ltrim($endpoint, '/');

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
     *
     * @param string $key
     * @param array $data
     * @param int $ttl
     */
    private function setToCache(string $key, array $data, int $ttl): void
    {
        $cacheDir = sys_get_temp_dir() . '/api_cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cacheFile = $this->getCacheFilePath($key);
        $cacheData = [
            'data' => $data,
            'expires' => time() + $ttl,
            'created' => time()
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
}
