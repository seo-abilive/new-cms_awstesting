<?php

namespace App\Mod\Content\Domain;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Mod\Content\Domain\Models\Content;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

/**
 * AIサービス
 */
class AiService extends AbstractService
{

    public function __construct(Content $model)
    {
        parent::__construct($model);
    }

    /**
     * AI添削
     */
    public function proofread(Request $request): array
    {
        // JSONボディからデータを取得
        $requestData = json_decode($request->getContent(), true) ?? [];
        $text = $requestData['text'] ?? '';
        $prompt = $requestData['prompt'] ?? AiType::PROOFREAD;
        $promptText = $requestData['prompt_text'] ?? '';
        $is_html = $requestData['is_html'] ?? false;

        if (empty(trim($text))) {
            return [
                'success' => false,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'error' => 'テキストが空です'
                ]
            ];
        }

        $apiKey = $this->findApiKey($request);
        if (empty($apiKey)) {
            return [
                'success' => false,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'error' => 'APIキーが設定されていません'
                ]
            ];
        }

        try {
            // HTMLをプレーンテキストに変換（改行を保持）
            // <p>タグは段落区切り（改行2つ）、<br>タグは改行（改行1つ）に変換
            $plainText = $text;
            $plainText = preg_replace('/<p[^>]*>/i', "\n\n", $plainText);
            $plainText = preg_replace('/<\/p>/i', '', $plainText);
            $plainText = preg_replace('/<br\s*\/?>/i', "\n", $plainText);
            $plainText = preg_replace('/<\/?div[^>]*>/i', "\n", $plainText);
            $plainText = strip_tags($plainText);
            $plainText = html_entity_decode($plainText, ENT_QUOTES, 'UTF-8');
            $plainText = preg_replace('/\n{3,}/', "\n\n", $plainText); // 連続する改行を2つまでに
            $plainText = trim($plainText);

            // 文字数制限（無料枠を考慮して5000文字まで）
            if (mb_strlen($plainText) > 5000) {
                $plainText = mb_substr($plainText, 0, 5000);
            }

            // Gemini APIを呼び出し
            $endpoint = trim(config('services.gemini.api_endpoint', ''));

            if (empty($endpoint)) {
                return [
                    'success' => false,
                    'timestamp' => now()->timestamp,
                    'payload' => [
                        'error' => 'APIエンドポイントが設定されていません'
                    ]
                ];
            }

            // URLの構築（エンドポイントに既にクエリパラメータがある場合とない場合に対応）
            $url = $endpoint;
            $separator = strpos($url, '?') !== false ? '&' : '?';
            $url .= $separator . 'key=' . urlencode($apiKey);

            $response = Http::timeout(30)->post(
                $url,
                [
                    'contents' => [[
                        'parts' => [[
                            'text' => AiType::getPrompt($prompt, $plainText, $promptText)
                        ]]
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 2000,
                    ]
                ]
            );

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'timestamp' => now()->timestamp,
                    'payload' => [
                        'error' => 'AI添削サービスでエラーが発生しました'
                    ]
                ];
            }

            $responseData = $response->json();
            $rawProofreadText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($rawProofreadText)) {
                return [
                    'success' => false,
                    'timestamp' => now()->timestamp,
                    'payload' => [
                        'error' => '添削結果が取得できませんでした'
                    ]
                ];
            }

            // レスポンスから説明文を除去し、添削された文章のみを抽出
            // 「添削結果:」「改善された文章:」などの説明文を除去
            $proofreadText = trim($rawProofreadText);

            // 説明文のパターンを除去
            $patterns = [
                '/^添削結果[：:]\s*/u',
                '/^改善された文章[：:]\s*/u',
                '/^以下が添削された文章です[：:]\s*/u',
                '/^添削後の文章[：:]\s*/u',
                '/^改善版[：:]\s*/u',
            ];

            foreach ($patterns as $pattern) {
                $proofreadText = preg_replace($pattern, '', $proofreadText);
            }

            $proofreadText = trim($proofreadText);

            // プレーンテキストをHTML形式に変換（改行を<p>タグに）
            // 改行2つ以上で段落区切り、改行1つで<br>タグ
            $proofreadHtml = $proofreadText;
            if ($is_html) {
                $proofreadHtml = preg_replace('/\n\n+/', '</p><p>', $proofreadHtml);
                $proofreadHtml = preg_replace('/\n/', '<br>', $proofreadHtml);
                $proofreadHtml = '<p>' . $proofreadHtml . '</p>';
                $proofreadHtml = preg_replace('/<p>\s*<\/p>/', '', $proofreadHtml); // 空の段落を除去
                $proofreadHtml = preg_replace('/<p><br><\/p>/', '', $proofreadHtml); // <p><br></p>を除去
            }

            return [
                'success' => true,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'data' => [
                        'original' => $text,
                        'proofread' => $proofreadHtml, // HTML形式で返す
                        'original_plain' => $plainText,
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('AI Proofread Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'error' => '添削処理中にエラーが発生しました: ' . $e->getMessage()
                ]
            ];
        }
    }

    protected function findApiKey(Request $request): string
    {
        $companyAlias = $request->route('company_alias');
        $company = ContractCompany::where('alias', $companyAlias)->firstOrFail();
        return $company?->ai_api_key ?? '';
    }
}
