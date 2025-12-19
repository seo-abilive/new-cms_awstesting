<?php

namespace App\Services;

use Core\Pagination;

class BaseService
{
    protected string $facilityAlias = 'master';

    public function __construct(string $facilityAlias = 'master')
    {
        $this->facilityAlias = $facilityAlias;
    }

    /**
     * エンドポイントインスタンスを取得
     * @param string $name エンドポイント名
     * @param array $options[
     *  'endpoint' => string,
     *  'cache' => int,
     *  'token' => string,
     *  'facility' => string,
     *  'params' => array,
     *  'headers' => array,
     *  ] オプション
     * 
     * @return ApiService
     */
    public function getApiServiceInstance(string $name, array $options = []): ApiService
    {
        // デフォルト値を設定
        $options = \array_merge([
            'cache' => 60,
            'token' => '',
            'facility' => $this->facilityAlias,
            'params' => [],
            'headers' => [],
        ], $options);

        $apiService = new ApiService($name);
        $apiService->headers($options['headers']);
        $apiService->enableCache($options['cache']);
        $apiService->setToken($options['token']);
        $apiService->params($options['params']);
        $apiService->addFacility($options['facility']);


        if (isset($options['endpoint']) && (string)$options['endpoint'] !== '') {
            $apiService->endpoint($options['endpoint']);
        }

        return $apiService;
    }

    /**
     * 一覧を取得
     */
    public function findList(ApiService $apiService): array
    {
        return $apiService->getList();
    }

    /**
     * 詳細を取得
     */
    public function findDetail(ApiService $apiService, int $id): array
    {
        return $apiService->getDetail($id);
    }

    /**
     * カテゴリ一覧を取得
     */
    public function findCategories(ApiService $apiService): array
    {
        return $apiService->getCategories();
    }

    /**
     * 複数のエンドポイントを取得
     */
    public function findMultiple(array $apiServices): array
    {
        return (new ApiService())->getMultiple($apiServices);
    }

    /**
     * 緊急のお知らせを取得
     */
    public function findEmergencyNews(): array
    {
        $emergencyInstance = $this->getApiServiceInstance('emergency', [
            'token' => API_EMERGENCY_TOKEN,
            'params' => ['limit' => 1]
        ]);

        $response = $this->findMultiple(['emergency' => $emergencyInstance]);
        return !empty($response['emergency']['contents']) ? $response['emergency']['contents'][0] : [];
    }

    /**
     * 新着情報一覧インスタンスを取得
     */
    public function getNewsListInstance($limit = 10, $query = [], $endpoint = '', $sort = 'public_date', $direction = 'desc'): ApiService
    {
        $params = [
            'limit' => $limit,
            'sort' => $sort,
            'direction' => $direction,
            'criteria' => []
        ];

        // カテゴリでの絞り込み
        if (isset($query['category']) && $query['category'] !== '') {
            $params['criteria']['category'] = $query['category'];
        }

        // ページング
        if (isset($query['page']) && $query['page'] !== '') {
            $params['current'] = $query['page'];
        }

        return $this->getApiServiceInstance($endpoint, [
            'token' => API_NEWS_TOKEN,
            'params' => $params,
            'endpoint' => $endpoint
        ]);
    }

    /**
     * 新着情報詳細インスタンスを取得
     */
    public function getNewsDetailInstance(int $id, $sort = 'public_date', $direction = 'desc'): ApiService
    {
        return $this->getApiServiceInstance('news', [
            'token' => API_NEWS_TOKEN,
            'endpoint' => "news/{$id}",
            'params' => ['criteria' => ['page_type' => 'detail'], 'sort' => $sort, 'direction' => $direction]
        ]);
    }

    /**
     * ページネーションを生成
     */
    public function generatePagination(array $listData, string $baseUrl = './', array $query = []): ?array
    {
        $pagination = null;

        if ($listData && isset($listData['all'])) {
            $paginationData = [
                'total' => $listData['all'],
                'current' => $listData['current'] ?? $query['page'] ?? 1,
                'pages' => $listData['pages'],
                'limit' => $listData['limit']
            ];

            $pagination = (new Pagination($paginationData, $baseUrl, $query))->generate();
        }

        return $pagination;
    }

    /**
     * カテゴリ取得（全件含む）
     */
    public function findCategoriesAndAll(array $query = [], string $endpoint = 'news', string $token = ''): array
    {
        $selected = $query['category'] ?? null;

        // カテゴリ取得
        $data = $this->getApiServiceInstance($endpoint, [
            'token' => $token,
        ])->getCategories();

        $all = 0;
        $categories = [];
        foreach ($data ? $data['contents'] : [] as $category) {
            $all += $category['contents_count'];
            if ($category['contents_count'] > 0) {
                $categories[] = $category;
            }
        }

        return [
            'all' => $all,
            'items' => $categories,
            'active' => $selected
        ];
    }

    /**
     * カテゴリとそれに紐づくアイテムに変換
     */
    public function formatCategoryToItems(array $categories, array $items): array
    {
        $list = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $cateItems = array_filter($items ?? [], function ($item) use ($category) {
                    $cateIds = array_map(function ($cate) {
                        return $cate['id'];
                    }, ($item['categories'] ?? []));
                    return in_array($category['id'], $cateIds);
                });

                $list[] = [
                    'category' => $category,
                    'items' => $cateItems
                ];
            }
        }

        return $list;
    }
}
