# フロント API 取得用サービス

デモサイトで CMS API からデータを取得するための統合 API サービスです。チェーンメソッドで柔軟に設定でき、`enableCache()`を呼ぶことでキャッシュ機能を有効化できます。

## 構成

### ApiService.php

API 通信とキャッシュ機能を統合したサービスクラスです。

**主な機能:**

-   GET/POST リクエストの送信
-   チェーンメソッドによる設定
-   エンドポイント指定
-   パラメータ・ヘッダー設定
-   キャッシュ機能（`enableCache()`で有効化）
-   エラーハンドリング

**使用例:**

```php
use App\Services\ApiService;

// 基本的な使用方法（キャッシュ無効）
$data = (new ApiService('news'))
    ->params(['limit' => 10, 'status' => 'published'])
    ->getList();

// キャッシュ有効で使用
$data = (new ApiService('news'))
    ->enableCache(1800) // 30分キャッシュ
    ->params(['limit' => 10])
    ->getList();

// 詳細取得
$detail = (new ApiService('news'))
    ->enableCache(1800)
    ->getDetail(123);

// カスタムエンドポイント
$customData = (new ApiService())
    ->endpoint('custom/endpoint')
    ->enableCache(3600)
    ->params(['id' => 456])
    ->get();
```

## チェーンメソッド

### 設定メソッド

-   `endpoint(string $endpoint)` - エンドポイントを設定
-   `enableCache(?int $ttl = null)` - キャッシュを有効化（TTL 指定可能）
-   `disableCache()` - キャッシュを無効化
-   `timeout(int $timeout)` - タイムアウト時間を設定
-   `headers(array $headers)` - ヘッダーを追加
-   `params(array $params)` - パラメータを追加
-   `baseEndpoint(string $endpoint)` - ベースエンドポイントを設定

### 実行メソッド

-   `get(?string $endpoint = null, array $params = [], array $headers = [])` - GET リクエスト
-   `post(?string $endpoint = null, array $data = [], array $headers = [])` - POST リクエスト
-   `getList(array $params = [])` - 一覧取得（汎用）
-   `getDetail(int $id)` - 詳細取得（汎用）

### キャッシュ管理メソッド

-   `clearCache(?string $pattern = null)` - キャッシュをクリア
-   `isCacheEnabled()` - キャッシュが有効かどうかを確認

## 使用例

### 基本的な使用方法

```php
// ニュース一覧を取得（キャッシュ有効）
$newsList = (new ApiService('news'))
    ->enableCache(1800) // 30分キャッシュ
    ->params(['limit' => 10, 'status' => 'published'])
    ->getList();

// ニュース詳細を取得（キャッシュ有効）
$newsDetail = (new ApiService('news'))
    ->enableCache(1800)
    ->getDetail(123);

// キャッシュ無効で取得
$data = (new ApiService('news'))
    ->disableCache()
    ->getList();
```

### 複雑な設定例

```php
// 複数の設定をチェーンで行う
$data = (new ApiService('content/articles'))
    ->enableCache(3600) // 1時間キャッシュ
    ->timeout(15) // 15秒タイムアウト
    ->params([
        'limit' => 20,
        'status' => 'published',
        'order_by' => 'created_at',
        'order_direction' => 'desc'
    ])
    ->headers([
        'X-Custom-Header' => 'custom-value',
        'Accept-Language' => 'ja'
    ])
    ->getList();
```

### コントローラーでの使用例

```php
<?php
namespace App\Controllers;

use Core\View;
use App\Services\ApiService;

class NewsController
{
    public function index()
    {
        // チェーンメソッドでニュース一覧を取得（キャッシュ有効）
        $newsList = (new ApiService('news'))
            ->enableCache(1800) // 30分キャッシュ
            ->params(['limit' => 10, 'status' => 'published'])
            ->getList();

        return View::render('news/index', [
            'newsList' => $newsList ?? [],
            'pageTitle' => 'お知らせ'
        ]);
    }
}
```

## キャッシュ機能

### キャッシュの有効化

```php
// デフォルトのキャッシュ時間（1時間）で有効化
$data = (new ApiService('news'))->enableCache()->getList();

// カスタムキャッシュ時間で有効化
$data = (new ApiService('news'))->enableCache(1800)->getList(); // 30分
```

### キャッシュの無効化

```php
// キャッシュを無効化
$data = (new ApiService('news'))->disableCache()->getList();
```

### キャッシュのクリア

```php
// 全てのキャッシュをクリア
(new ApiService())->clearCache();

// 特定のパターンのキャッシュをクリア
(new ApiService())->clearCache('news_*');
```

## 設定

### 環境設定

`_system/app/config.php`で環境別の API エンドポイントを設定できます。

```php
// 開発環境
define('BASE_ENDPOINT', 'http://localhost:8000/api/v1/');

// 本番環境
define('BASE_ENDPOINT', 'https://example.com/api/v1/');
```

### API 設定

`_system/app/config/api.php`で API の詳細設定を行えます。

```php
return [
    'api' => [
        'base_endpoint' => BASE_ENDPOINT,
        'timeout' => 30,
        'retry_count' => 3,
    ],
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1時間
        'prefix' => 'api_cache_',
    ],
];
```

## テスト実行

```bash
cd demo/_system
php test_api_service.php
```

## エラーハンドリング

-   API 接続エラー時は`null`を返します
-   デバッグモード時はエラーログを出力します
-   キャッシュエラー時は通常の API リクエストにフォールバックします

## パフォーマンス

-   `enableCache()`を呼ぶことでキャッシュ機能が有効化されます
-   デフォルトで 1 時間のキャッシュ時間を設定
-   必要に応じてキャッシュをクリア可能
-   キャッシュが無効な場合は通常の API リクエストを実行

## 注意事項

-   API エンドポイントが正しく設定されていることを確認してください
-   キャッシュディレクトリの書き込み権限を確認してください
-   本番環境ではデバッグモードを無効にしてください
-   `enableCache()`を呼ばない限りキャッシュ機能は無効です
