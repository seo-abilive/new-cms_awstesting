<?php

namespace App\Services;

class GroupService extends BaseService
{

    public function findTopPage(): array
    {
        // TOPバナー
        $topBannerInstance = $this->getApiServiceInstance('top_banner', [
            'token' => API_TOP_BANNER_TOKEN,
            'params' => ['mode' => 'all']
        ]);

        // 新着情報
        $newsInstance = $this->getNewsListInstance(3, [], 'news');

        // データ取得
        $response = $this->findMultiple([
            'top_banner' => $topBannerInstance,
            'news' => $newsInstance
        ]);

        return [
            'top_banner' => !empty($response['top_banner']['contents']) ? $response['top_banner']['contents'] : [],
            'news' => !empty($response['news']['contents']) ? $response['news']['contents'] : []
        ];
    }

    public function findNewsListPage(int $limit = 10, array $query = []): array
    {
        // 新着一覧
        $newsInstance = $this->getNewsListInstance($limit, $query, 'news');

        // マークアップ
        $markupInstance = $this->getNewsListInstance($limit, $query, 'news/markup');

        $response = $this->findMultiple([
            'news' => $newsInstance,
            'markup' => $markupInstance
        ]);

        // ページネーション
        $pagination = $this->generatePagination($response['news'], './', $query);

        return [
            'news' => !empty($response['news']['contents']) ? $response['news']['contents'] : [],
            'pagination' => $pagination,
            'markup' => !empty($response['markup']['contents']) ? $response['markup']['contents'] : []
        ];
    }

    public function findNewsDetailPage(int $id): array
    {
        // 新着詳細
        $newsInstance = $this->getNewsDetailInstance($id, 'news');

        // マークアップ
        $markupInstance = $this->getNewsDetailInstance($id, 'news/markup');

        $response = $this->findMultiple([
            'news' => $newsInstance,
            'markup' => $markupInstance
        ]);


        return [
            'news' => !empty($response['news']['contents']) ? $response['news']['contents'] : [],
            'sibLings' => $response['news']['sibLings'],
            'markup' => !empty($response['markup']['contents']) ? $response['markup']['contents'] : []
        ];
    }

    public function findFaqListPage(): array
    {
        // カテゴリ取得
        $categoryInstance = $this->getApiServiceInstance('faq/categories', [
            'token' => API_FAQ_TOKEN,
        ]);

        // FAQ一覧
        $faqInstance = $this->getApiServiceInstance('faq', [
            'token' => API_FAQ_TOKEN,
            'params' => ['mode' => 'all']
        ]);

        // マークアップ
        $markupInstance = $this->getApiServiceInstance('faq/markup', [
            'token' => API_FAQ_TOKEN,
            'params' => ['mode' => 'all']
        ]);

        $response = $this->findMultiple([
            'categories' => $categoryInstance,
            'faq' => $faqInstance,
            'markup' => $markupInstance
        ]);

        $faqList = $this->formatCategoryToItems(
            !empty($response['categories']['contents']) ? $response['categories']['contents'] : [],
            !empty($response['faq']['contents']) ? $response['faq']['contents'] : []
        );

        return [
            'faq' => $faqList,
            'markup' => !empty($response['markup']['contents']) ? $response['markup']['contents'] : []
        ];
    }


    public function findHotelListPage(): array
    {
        // 施設設定を取得
        $hotelInstance = $this->getApiServiceInstance('facility_setting', [
            'token' => API_FACILITY_SETTING_TOKEN,
            'params' => ['mode' => 'all'],
            'facility' => ''
        ]);

        $response = $this->findMultiple(['facility_setting' => $hotelInstance]);

        return [
            'hotel' => !empty($response['facility_setting']['contents']) ? $response['facility_setting']['contents'] : []
        ];
    }
}
