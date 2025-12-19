<?php
namespace Core;

/**
 * View class
 */
class View
{
    private static $viewInstance = null;
    private array $data = [];

    /**
     * Viewインスタンスを取得
     */
    public static function getInstance(): self
    {
        if (self::$viewInstance === null) {
            self::$viewInstance = new self();
        }
        return self::$viewInstance;
    }

    /**
     * 外部ディレクトリからViewファイルを読み込む
     */
    public static function render(string $template, array $data = []): void
    {
        
        // データを個別変数として展開
        extract($data, EXTR_SKIP);
        unset($data);

        // ルートパスからテンプレートファイルのパスを取得
        $path = __DIR__ . '/../../' . $template . '.php';

        if (!file_exists($path)) {
            \http_response_code(500);
            echo "View not found: {$path}";
            return;
        }

        require $path;
    }

    /**
     * 部分テンプレートをレンダリングして文字列として返す
     */
    public static function renderPartial(string $template, array $data = []): string
    {
        ob_start();
        self::render($template, $data);
        return ob_get_clean();
    }

    /**
     * _system/app/Viewsから部分テンプレートをレンダリング
     */
    public static function renderSystemPartial(string $template, array $data = []): string
    {
        ob_start();
        self::renderSystemView($template, $data);
        return ob_get_clean();
    }

    /**
     * _system/app/ViewsからViewファイルを読み込む
     */
    public static function renderSystemView(string $template, array $data = []): void
    {
        // データを個別変数として展開
        extract($data, EXTR_SKIP);

        // _system/app/Viewsからテンプレートファイルのパスを取得
        $path = __DIR__ . '/../app/Views/' . $template . '.html.php';

        if (!file_exists($path)) {
            \http_response_code(500);
            echo "System View not found: {$path}";
            return;
        }

        require $path;
    }

    /**
     * View変数を設定
     */
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * View変数を取得
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * 複数のView変数を設定
     */
    public function setData(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * 部分テンプレートをレンダリング（インスタンスメソッド）
     */
    public function partial(string $template, array $additionalData = []): string
    {
        $data = array_merge($this->data, $additionalData);
        return self::renderSystemPartial($template, $data);
    }

    /**
     * 全てのView変数を取得
     */
    public function getAllData(): array
    {
        return $this->data;
    }
}