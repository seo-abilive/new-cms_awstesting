<?php

namespace App\Http\Middleware;

use App\Mod\ActionLog\Domain\Models\ActionLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActionLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $user = auth()->user();

        $actionLog = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'path' => $request->path(),
            'params' => $this->sanitizeParams($request->all()),
            'user_id' => $user ? $user->id : null
        ];

        $response = $next($request);

        // 実行時間を計測
        $duration = microtime(true) - $startTime;
        $actionLog += [
            'duration' => $duration,
            'http_status' => $response->getStatusCode(),
            // エラーメッセージを取得（存在する場合のみ）
            'message' => method_exists($response, 'getContent') && $response->getStatusCode() >= 400
                ? $this->getErrorMessage($response)
                : null,
        ];

        // ログ保存
        ActionLog::create($actionLog);

        return $response;
    }

    private function sanitizeParams(array $params): array
    {
        $mask = ['password', 'token', 'api_key'];

        foreach ($mask as $key) {
            if (isset($params[$key])) {
                $params[$key] = '******';
            }
        }

        return $params;
    }

    private function getErrorMessage(Response $response): ?string
    {
        $content = $response->getContent();
        if ($content === '' || $content === null) {
            return null;
        }

        $contentType = $response->headers->get('Content-Type', '');

        // JSON レスポンスの場合は優先的に代表的なキーを参照
        if (is_string($contentType) && str_contains($contentType, 'application/json')) {
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                if (isset($data['message']) && is_string($data['message'])) {
                    return $data['message'];
                }
                if (isset($data['error']) && is_string($data['error'])) {
                    return $data['error'];
                }
                if (isset($data['errors']) && is_array($data['errors'])) {
                    $flat = [];
                    foreach ($data['errors'] as $field => $msgs) {
                        if (is_array($msgs)) {
                            foreach ($msgs as $m) {
                                if (is_string($m)) {
                                    $flat[] = $m;
                                }
                            }
                        } elseif (is_string($msgs)) {
                            $flat[] = $msgs;
                        }
                    }
                    if (!empty($flat)) {
                        return implode(' | ', array_slice($flat, 0, 5));
                    }
                }
            }
        }

        // Fallback: HTMLやプレーンテキストから先頭200文字を抜粋
        $stripped = trim(strip_tags($content));
        if ($stripped !== '') {
            return mb_substr($stripped, 0, 200);
        }

        return null;
    }
}
