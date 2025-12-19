<?php
namespace App\Controllers;

use Core\View;

abstract class AbstractController
{
    protected $view;

    public function __construct()
    {
        $this->view = View::getInstance();
        // Viewインスタンス自体をView変数として設定
        $this->view->set('view', $this->view);
    }

    /**
     * テンプレートをレンダリング
     */
    protected function render(string $template, array $data = []): void
    {
        // View変数と追加データをマージ
        $mergedData = array_merge($this->view->getAllData(), $data);
        View::render($template, $mergedData);
    }

    /**
     * 部分テンプレートをレンダリング
     */
    protected function partial(string $template, array $additionalData = []): string
    {
        return $this->view->partial($template, $additionalData);
    }

    /**
     * View変数を設定
     */
    protected function setViewData(string $key, $value): self
    {
        $this->view->set($key, $value);
        return $this;
    }

    /**
     * 複数のView変数を設定
     */
    protected function setViewDataArray(array $data): self
    {
        $this->view->setData($data);
        return $this;
    }
}