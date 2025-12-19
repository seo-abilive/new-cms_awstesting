# View部分レンダリング機能（AbstractController対応版）

`_system`経由でView変数を渡し、部分的にレンダリングする機能の使用方法です。AbstractControllerを使用して共通化されています。

## 機能概要

- AbstractControllerでView機能を共通化
- Viewインスタンスで変数を管理
- 部分テンプレートのレンダリング
- `_system/app/Views`からの部分テンプレート読み込み（`.html.php`拡張子）
- チェーンメソッドでの変数設定

## 基本的な使用方法

### 1. コントローラーでの設定

```php
<?php
namespace App\Controllers;

use App\Services\ApiService;

class NewsController extends AbstractController
{
    public function index()
    {
        // データを取得
        $newsList = (new ApiService('news'))
            ->enableCache(60)
            ->params(['limit' => 10])
            ->getList();
        
        // テンプレートをレンダリング
        return $this->render('news/index', [
            'newsList' => $newsList ? $newsList['contents'] : []
        ]);
    }
}
```

### 2. AbstractControllerの機能

```php
abstract class AbstractController
{
    protected $view;

    public function __construct()
    {
        $this->view = View::getInstance();
        $this->view->set('view', $this->view);
    }

    // テンプレートをレンダリング
    protected function render(string $template, array $data = []): void

    // 部分テンプレートをレンダリング
    protected function partial(string $template, array $additionalData = []): string

    // View変数を設定
    protected function setViewData(string $key, $value): self

    // 複数のView変数を設定
    protected function setViewDataArray(array $data): self
}
```

### 3. テンプレートでの部分レンダリング

```php
<?php
// メインテンプレート内で部分テンプレートを使用
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
</head>
<body>
    <main>
        <!-- ニュース一覧の部分テンプレート -->
        <?php echo $view->partial('news/news_list', ['newsList' => $newsList]); ?>
        
        <!-- APIデータ取得の部分テンプレート -->
        <?php echo $view->partial('api/data_fetch', [
            'endpoint' => 'news',
            'limit' => 5,
            'title' => '最新ニュース'
        ]); ?>
    </main>
</body>
</html>
```

## Viewクラスのメソッド

### インスタンスメソッド

- `set(string $key, $value)` - 単一の変数を設定
- `get(string $key, $default = null)` - 変数を取得
- `setData(array $data)` - 複数の変数を設定
- `getAllData()` - 全ての変数を取得
- `partial(string $template, array $additionalData = [])` - 部分テンプレートをレンダリング

### 静的メソッド

- `getInstance()` - Viewインスタンスを取得
- `render(string $template, array $data = [])` - メインテンプレートをレンダリング
- `renderPartial(string $template, array $data = [])` - 部分テンプレートをレンダリング
- `renderSystemPartial(string $template, array $data = [])` - システム部分テンプレートをレンダリング

## 部分テンプレートの作成

### ファイル配置

```
demo/_system/app/Views/
├── news/
│   ├── news_list.html.php      # ニュース一覧
│   └── news_detail.html.php    # ニュース詳細
├── api/
│   └── data_fetch.html.php     # APIデータ取得
├── common/
│   └── static_content.html.php # 静的コンテンツ
└── examples/
    └── partial_usage.html.php  # 使用例
```

### 部分テンプレートの例

```php
<?php
/**
 * ニュース一覧の部分テンプレート
 * 使用例: $view->partial('news/news_list', ['newsList' => $newsList])
 */
?>

<?php if (!empty($newsList)): ?>
<div class="news-list">
    <h2 class="news-title"><?php echo htmlspecialchars($pageTitle ?? 'お知らせ'); ?></h2>
    <ul class="news-items">
        <?php foreach ($newsList as $news): ?>
        <li class="news-item">
            <h3 class="news-item-title">
                <a href="/news/<?php echo $news['id']; ?>">
                    <?php echo htmlspecialchars($news['title'] ?? ''); ?>
                </a>
            </h3>
            <p class="news-item-content">
                <?php echo htmlspecialchars($news['content'] ?? ''); ?>
            </p>
            <div class="news-meta">
                <span class="news-date">
                    <?php echo date('Y.m.d', strtotime($news['created_at'] ?? '')); ?>
                </span>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php else: ?>
<div class="news-list">
    <p class="no-news">お知らせはありません。</p>
</div>
<?php endif; ?>
```

## 使用例

### 1. 基本的な部分レンダリング

```php
// コントローラー
class NewsController extends AbstractController
{
    public function index()
    {
        $newsList = (new ApiService('news'))->getList();
        return $this->render('news/index', ['newsList' => $newsList]);
    }
}

// テンプレート
echo $view->partial('news/news_list');
```

### 2. 追加データ付きの部分レンダリング

```php
// テンプレート
echo $view->partial('news/news_list', [
    'newsList' => $additionalNews,
    'showDate' => true
]);
```

### 3. View変数の設定

```php
// コントローラー
class NewsController extends AbstractController
{
    public function index()
    {
        // View変数を設定
        $this->setViewData('pageTitle', 'お知らせ');
        $this->setViewDataArray([
            'siteSettings' => $settings,
            'apiService' => new ApiService()
        ]);
        
        $newsList = (new ApiService('news'))->getList();
        return $this->render('news/index', ['newsList' => $newsList]);
    }
}
```

## 修正内容

### PHP 8.2対応
- 動的プロパティの作成を非推奨化に対応
- `private array $data = []`でプロパティを明示的に定義
- `$this->$key`の代わりに`$this->data[$key]`を使用

### AbstractController共通化
- View機能をAbstractControllerで共通化
- `render()`, `partial()`, `setViewData()`メソッドを提供
- コントローラーでのコード重複を削減

### ファイル拡張子
- 部分テンプレートの拡張子を`.html.php`に統一
- より明確なファイル識別が可能

## 注意事項

- 部分テンプレートは`_system/app/Views/`ディレクトリに配置してください
- ファイル拡張子は`.html.php`を使用してください
- View変数は自動的に部分テンプレートに渡されます
- 追加データは`partial()`メソッドの第2引数で指定できます
- AbstractControllerを継承することで共通機能が利用できます