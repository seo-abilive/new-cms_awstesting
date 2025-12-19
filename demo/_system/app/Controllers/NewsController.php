<?php

namespace App\Controllers;

use Core\View;
use Core\Pagination;
use App\Services\ApiService;

class NewsController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
        $this->setCategories();
    }

    public function index()
    {
        $params = ['limit' => 10];

        $category = $_GET['category'] ?? null;
        if ($category) {
            $params['criteria']['category'] = $category;
        }

        $page = $_GET['page'] ?? 1;
        if ($page) {
            $params['current'] = $page;
        }

        // チェーンメソッドでニュース一覧を取得（キャッシュ有効）
        $newsList = (new ApiService('news'))
            ->enableCache(60) // 1分キャッシュ
            ->params($params)
            ->setToken(API_NEWS_TOKEN)
            ->addFacility('demo')
            ->getList();

        // マークアップデータを取得
        $markupData = (new ApiService('news'))
            ->enableCache(60)
            ->setToken(API_NEWS_TOKEN)
            ->params($params)
            ->addFacility('demo')
            ->getMarkupList('news');

        // ページネーション
        $pagination = null;
        if ($newsList && isset($newsList['all'])) {
            $paginationData = [
                'total' => $newsList['all'],
                'current' => $newsList['current'] ?? $page,
                'pages' => $newsList['pages'],
                'limit' => $newsList['limit']
            ];

            $pagination = (new Pagination($paginationData, './', $_GET))
                ->setMaxVisiblePages(5)
                ->generate();
        }

        return $this->render('news/index', [
            'cms__news_list' => $newsList ? $newsList['contents'] : [],
            'cms__pagination' => $pagination,
            'cms__markup_data' => $markupData
        ]);
    }

    public function detail(int $id)
    {
        // チェーンメソッドでニュース詳細を取得（キャッシュ有効）
        $newsDetail = (new ApiService('news'))
            ->enableCache(60) // 1分キャッシュ
            ->params(['criteria' => ['page_type' => 'detail']])
            ->setToken(API_NEWS_TOKEN)
            ->addFacility('demo')
            ->getDetail($id);

        if (!$newsDetail) {
            // ニュースが見つからない場合は404ページへ
            return View::render('errors/404', [], 404);
        }

        $markupData = (new ApiService('news'))
            ->enableCache(60)
            ->setToken(API_NEWS_TOKEN)
            ->addFacility('demo')
            ->getMarkupDetail('news', $id);

        return $this->render('news/detail', [
            'cms__news' => $newsDetail['contents'],
            'cms__sibLings' => $newsDetail['sibLings'],
            'cms__markup_data' => $markupData
        ]);
    }

    /**
     * プレビュー表示
     * POSTリクエストで受け取ったデータを使ってプレビューHTMLを生成
     */
    public function preview()
    {
        // POSTデータを取得
        $input = file_get_contents('php://input');
        $data = json_decode($input, true) ?? [];

        // データが空の場合は空のHTMLを返す
        if (empty($data)) {
            header('Content-Type: text/html; charset=utf-8');
            echo '';
            return;
        }

        // プレビュー用のデータを構築
        // detailメソッドと同様の構造でデータを準備
        $previewData = [
            'contents' => $data,
            'sibLings' => [
                'previous' => null,
                'next' => null,
            ]
        ];

        // マークアップデータを取得（プレビュー用）
        $markupData = (new ApiService('news'))
            ->enableCache(60)
            ->setToken(API_NEWS_TOKEN)
            ->addFacility('demo')
            ->getMarkupList('news');

        // プレビューHTMLを生成
        $this->render('news/detail', [
            'cms__news' => $previewData['contents'],
            'cms__sibLings' => $previewData['sibLings'],
            'cms__markup_data' => $markupData
        ]);
    }

    /**
     * カテゴリを設定
     */
    protected function setCategories()
    {
        $selected = $_GET['category'] ?? null;

        // カテゴリ取得
        $data = (new ApiService('news'))
            ->enableCache(60)
            ->setToken(API_NEWS_TOKEN)
            ->getCategories();

        $all = 0;
        $categories = [];
        foreach ($data ? $data['contents'] : [] as $category) {
            $all += $category['contents_count'];
            if ($category['contents_count'] > 0) {
                $categories[] = $category;
            }
        }

        // カテゴリをViewに渡す
        $this->view->set('cms__news_categories', ['all' => $all, 'items' => $categories, 'active' => $selected]);
    }
}
