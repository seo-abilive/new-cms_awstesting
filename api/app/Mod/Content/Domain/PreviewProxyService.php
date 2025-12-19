<?php

namespace App\Mod\Content\Domain;

use App\Mod\Content\Domain\Models\Content;
use App\Mod\Content\Domain\ContentService;
use App\Mod\Content\Domain\Models\ContentCategory;
use App\Mod\Content\Domain\Models\ContentValue;
use App\Mod\ContentField\Domain\Models\ContentField;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

/**
 * プレビュープロキシサービス
 *
 * @property Content $model
 * @property ContentModel $contentModel
 * @property ContentService $contentService
 */
class PreviewProxyService extends AbstractService
{
    protected $contentService;

    public function __construct(Content $model, ContentService $contentService)
    {
        parent::__construct($model);
        $this->contentService = $contentService;
        $this->setContentModel();
    }

    /**
     * 外部プレビューサーバーからHTMLを取得
     */
    public function fetchPreview(Request $request): array
    {
        // JSONボディからデータを取得
        $requestData = json_decode($request->getContent(), true) ?? [];
        $previewUrl = $requestData['preview_url'] ?? '';
        $data = $requestData['data'] ?? [];

        if (empty($previewUrl)) {
            return [
                'success' => false,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'error' => 'プレビューURLが指定されていません'
                ]
            ];
        }

        // URLの検証（基本的な検証のみ）
        if (!filter_var($previewUrl, FILTER_VALIDATE_URL)) {
            return [
                'success' => false,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'error' => '無効なプレビューURLです'
                ]
            ];
        }

        // Docker環境でのlocalhost変換
        // localhost:8082 -> new-cms-demo (demoコンテナ)
        // localhost:8083 -> new-cms-demo-chain (demo-chainコンテナ)
        $parsedUrl = parse_url($previewUrl);
        if (isset($parsedUrl['host']) && in_array($parsedUrl['host'], ['localhost', '127.0.0.1'])) {
            $port = $parsedUrl['port'] ?? 80;

            // ポート番号に基づいてコンテナ名に変換
            $containerMap = [
                8082 => ['name' => 'new-cms-demo', 'port' => 80],
                8083 => ['name' => 'new-cms-demo-chain', 'port' => 80],
                5174 => ['name' => 'new-cms-demo-next', 'port' => 5174], // Dockerコンテナ内でもポート5174で動作
            ];

            if (isset($containerMap[$port])) {
                $containerInfo = $containerMap[$port];
                $parsedUrl['host'] = $containerInfo['name'];

                // ポート番号を設定（コンテナ内のポート）
                $containerPort = $containerInfo['port'];

                // URLを再構築（ポートが80の場合は省略）
                $scheme = $parsedUrl['scheme'] ?? 'http';
                $host = $parsedUrl['host'];
                $path = $parsedUrl['path'] ?? '/';
                $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';

                // ポートが80の場合は省略、それ以外は含める
                if ($containerPort == 80) {
                    $previewUrl = "{$scheme}://{$host}{$path}{$query}";
                } else {
                    $previewUrl = "{$scheme}://{$host}:{$containerPort}{$path}{$query}";
                }
            }
        }

        try {
            // dataをContent Modelのデータ形式に変換
            $content = $this->convertDataToContentFormat($data, $request);
            $data = $content->toFlatFrontArray();

            // 外部サーバーにPOSTリクエストを送信
            // まずJSON形式で試行、失敗した場合はフォームデータ形式も試行
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'text/html,application/json',
                ])
                ->post($previewUrl, $data);

            // 404エラーの場合、フォームデータ形式でも試行
            if ($response->status() === 404 && !empty($data)) {

                $response = Http::timeout(30)
                    ->asForm()
                    ->withHeaders([
                        'Accept' => 'text/html,application/json',
                    ])
                    ->post($previewUrl, $data);
            }

            if (!$response->successful()) {
                $errorBody = $response->body();
                // レスポンスボディが長すぎる場合は切り詰める
                $errorBodyPreview = mb_strlen($errorBody) > 500
                    ? mb_substr($errorBody, 0, 500) . '...'
                    : $errorBody;

                Log::warning('プレビュー取得エラー', [
                    'url' => $previewUrl,
                    'status' => $response->status(),
                    'body' => $errorBodyPreview,
                    'request_data_keys' => array_keys($data),
                ]);

                // エラーメッセージに詳細情報を含める（開発環境向け）
                $errorMessage = 'プレビューの取得に失敗しました（HTTP ' . $response->status() . '）';
                if (config('app.debug')) {
                    $errorMessage .= ' - URL: ' . $previewUrl;
                }

                return [
                    'success' => false,
                    'timestamp' => now()->timestamp,
                    'payload' => [
                        'error' => $errorMessage,
                        'url' => $previewUrl,
                        'status' => $response->status(),
                    ]
                ];
            }

            // レスポンスボディを取得
            $html = $response->body();

            return [
                'success' => true,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'html' => $html
                ]
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $errorMessage = $e->getMessage();
            Log::error('プレビュー接続エラー', [
                'url' => $previewUrl,
                'error' => $errorMessage,
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            // デバッグモードの場合は詳細なエラーメッセージを返す
            $userMessage = 'プレビューサーバーに接続できませんでした';
            if (config('app.debug')) {
                $userMessage .= ' - ' . $errorMessage;
            }

            return [
                'success' => false,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'error' => $userMessage,
                    'url' => $previewUrl,
                ]
            ];
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('プレビュー取得エラー', [
                'url' => $previewUrl,
                'error' => $errorMessage,
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            // デバッグモードの場合は詳細なエラーメッセージを返す
            $userMessage = 'プレビューの取得中にエラーが発生しました';
            if (config('app.debug')) {
                $userMessage .= ' - ' . $errorMessage;
            }

            return [
                'success' => false,
                'timestamp' => now()->timestamp,
                'payload' => [
                    'error' => $userMessage,
                    'url' => $previewUrl,
                ]
            ];
        }
    }

    /**
     * データをContentモデルに変換
     */
    protected function convertDataToContentFormat(array $data, Request $request): Content
    {
        $content = new Content();
        $content->seq_id = 1;
        $fields = $this->contentModel->fields;

        // 紐付け先をセット
        $companyAndFacility = $this->findCompanyAndFacility($request);
        $company = $companyAndFacility['company'];
        $facility = $companyAndFacility['facility'];

        $content->company_id = $company->id;
        $content->assignable_type = get_class($facility);
        $content->assignable_id = $facility->id;

        $this->appendCategories($content, $data);
        $this->appendFieldValues($content, $fields, $data);

        return $content;
    }

    protected function appendCategories(Content $content, array $data): void
    {
        $category = new ContentCategory();
        $category->seq_id = 1;
        if ($data['categories'] && is_array($data['categories'])) {
            $category->title = $data['categories']['label'];
        }
        $content->categories->push($category);
    }

    /**
     * フィールドの値を追加
     */
    protected function appendFieldValues(Content $content, Collection $fields, array $data, int $seqId = 1, ?array $addValues = []): void
    {
        /** @param ContentField $field */
        foreach ($fields as $field) {
            $fieldId = $field->field_id;
            $fieldType = $field->field_type;

            if (!isset($data[$fieldId])) {
                continue;
            }

            $inputVal = $data[$fieldId];

            switch ($fieldType) {
                case 'custom_block':
                    // カスタムブロック
                    if (!\is_array($inputVal)) {
                        continue;
                    }

                    // カスタムブロック
                    foreach ($inputVal as $key => $value) {
                        $blockSeqId = $value['block_seq_id'];
                        $this->appendFieldValues(
                            $content,
                            ContentField::where('id', $value['field_id'])->get(),
                            $value['values'],
                            $seqId,
                            [
                                'block_id' => $field->id,
                                'block_seq_id' => $blockSeqId,
                                'sort_num' => $key + 1,
                            ]
                        );
                    }
                    break;
                case 'custom_field':
                default:
                    // 通常フィールド
                    $values = [
                        'field_id' => $field->id,
                        'value' => $inputVal,
                        'seq_id' => $seqId,
                    ] + $addValues;

                    $contentValue = new ContentValue();
                    foreach ($values as $key => $value) {
                        $contentValue->{$key} = $value;
                    }

                    $content->values->push($contentValue);
                    $seqId++;
                    break;
            }
        }
    }
}
