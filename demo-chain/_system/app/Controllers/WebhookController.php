<?php

namespace App\Controllers;

use App\Services\ApiService;

/**
 * WebHookコントローラー
 * CMSからのWebHookを受け取り、キャッシュをクリアする
 */
class WebhookController extends AbstractController
{
    /**
     * キャッシュクリアWebHook
     * POST /webhook/cache/clear?type=all&value=news
     * 
     * リクエストボディの形式は送信側のプロダクトによるため、柔軟に対応
     * クエリパラメータまたはリクエストボディから type と value を取得
     *
     * クエリパラメータ:
     * - type: "all" | "model" | "endpoint" | "pattern"
     * - value: "news" (typeがmodel, endpoint, patternの場合)
     */
    public function clearCache(): void
    {
        // Webhookが到達しているか確認するため、受信データをログに出力
        $requestBody = file_get_contents('php://input');
        $this->logRequest($requestBody);

        // まずクエリパラメータから取得を試行
        $type = $_GET['type'] ?? null;
        $value = $_GET['value'] ?? null;

        // リクエストボディがあれば、そこからも取得を試行
        $bodyData = $this->parseRequestBody();
        if ($bodyData !== null) {
            // リクエストボディから type と value を探す（様々な形式に対応）
            if ($type === null) {
                $type = $bodyData['type'] ?? $bodyData['cache_type'] ?? $bodyData['action'] ?? null;
            }
            if ($value === null) {
                $value = $bodyData['value'] ?? $bodyData['cache_value'] ?? $bodyData['model'] ?? $bodyData['endpoint'] ?? $bodyData['pattern'] ?? null;
            }
        }

        // typeが指定されていない場合はデフォルトで'all'
        $type = $type ?? 'all';

        $result = [
            'success' => true,
            'type' => $type,
            'cleared_count' => 0,
        ];

        try {
            switch ($type) {
                case 'all':
                    // すべてのキャッシュをクリア
                    $success = ApiService::clearAllCache();
                    $result['success'] = $success;
                    $result['message'] = 'All cache cleared';
                    break;

                case 'model':
                    // モデル名でキャッシュをクリア
                    if (empty($value)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Value is required for model type']);
                        return;
                    }
                    $count = ApiService::clearCacheByModel($value);
                    $result['cleared_count'] = $count;
                    $result['value'] = $value;
                    $result['message'] = "Cache cleared for model: {$value}";
                    $result['cache_dir'] = sys_get_temp_dir() . '/api_cache/';
                    break;

                case 'endpoint':
                    // エンドポイントでキャッシュをクリア
                    if (empty($value)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Value is required for endpoint type']);
                        return;
                    }
                    $count = ApiService::clearCacheByEndpoint($value);
                    $result['cleared_count'] = $count;
                    $result['value'] = $value;
                    $result['message'] = "Cache cleared for endpoint: {$value}";
                    break;

                case 'pattern':
                    // パターンでキャッシュをクリア
                    if (empty($value)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Value is required for pattern type']);
                        return;
                    }
                    $count = ApiService::clearCacheByPattern($value);
                    $result['cleared_count'] = $count;
                    $result['value'] = $value;
                    $result['message'] = "Cache cleared for pattern: {$value}";
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid type. Must be: all, model, endpoint, or pattern']);
                    return;
            }

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * リクエストボディをパース（様々な形式に対応）
     *
     * @return array|null
     */
    private function parseRequestBody(): ?array
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return null;
        }

        // JSON形式を試行
        $jsonData = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
            return $jsonData;
        }

        // フォームデータ形式を試行
        parse_str($input, $formData);
        if (!empty($formData)) {
            return $formData;
        }

        // POSTデータを試行
        if (!empty($_POST)) {
            return $_POST;
        }

        return null;
    }

    /**
     * リクエストボデタをログに出力
     *
     * @param string $requestBody
     */
    protected function logRequest(string $requestBody): void
    {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/webhook_request.log';
        $logEntry = '[' . date('Y-m-d H:i:s') . '] ' . $requestBody . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
