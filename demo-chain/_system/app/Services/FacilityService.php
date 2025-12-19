<?php

namespace App\Services;

use App\Services\BaseService;

class FacilityService extends BaseService
{

    public function findCommon(): array
    {
        // 緊急のお知らせ取得
        $emergencyInstance = $this->getApiServiceInstance('emergency', [
            'token' => API_EMERGENCY_TOKEN,
            'params' => ['limit' => 1]
        ]);

        // 施設設定を取得
        $facilitySettingInstance = $this->getApiServiceInstance('facility_setting', [
            'token' => API_FACILITY_SETTING_TOKEN,
            'params' => ['limit' => 1],
        ]);

        // データ取得
        $response = $this->findMultiple([
            'emergency' => $emergencyInstance,
            'facility_setting' => $facilitySettingInstance
        ]);

        return [
            'emergency' => !empty($response['emergency']['contents']) ? $response['emergency']['contents'][0] : [],
            'facility_setting' => !empty($response['facility_setting']['contents']) ? $response['facility_setting']['contents'][0] : []
        ];
    }

    public function findPageSetting($targetPage): array
    {
        $pageSettingInstance = $this->getApiServiceInstance('page_setting', [
            'token' => API_PAGE_SETTING_TOKEN,
            'params' => ['limit' => 1, 'criteria' => ['target_page' => $targetPage]]
        ]);

        $response = $this->findMultiple(['page_setting' => $pageSettingInstance]);

        return !empty($response['page_setting']['contents']) ? $response['page_setting']['contents'][0] : [];
    }

    public function findTopPage(): array
    {
        // TOPページ設定を取得
        $topPageSettingInstance = $this->getApiServiceInstance('top_page_setting', [
            'token' => API_TOP_PAGE_SETTING_TOKEN,
            'params' => ['limit' => 1]
        ]);

        // TOPバナーを取得
        $topBannerInstance = $this->getApiServiceInstance('top_banner', [
            'token' => API_TOP_BANNER_TOKEN,
            'params' => ['mode' => 'all']
        ]);

        // 新着情報を取得
        $newsInstance = $this->getNewsListInstance(3, [], 'news');

        // データ取得
        $response = $this->findMultiple([
            'top_page_setting' => $topPageSettingInstance,
            'top_banner' => $topBannerInstance,
            'news' => $newsInstance
        ]);

        return [
            'top_page_setting' => !empty($response['top_page_setting']['contents']) ? $response['top_page_setting']['contents'][0] : [],
            'top_banner' => !empty($response['top_banner']['contents']) ? $response['top_banner']['contents'] : [],
            'news' => !empty($response['news']['contents']) ? $response['news']['contents'] : []
        ];
    }

    public function findNewsListPage(int $limit = 10, array $query = []): array
    {
        // 新着情報を取得
        $newsInstance = $this->getNewsListInstance($limit, $query, 'news');

        // データ取得
        $response = $this->findMultiple(['news' => $newsInstance]);

        // ページネーション
        $pagination = $this->generatePagination($response['news'], WEB_ROOT . $this->facilityAlias . '/news/', $query);

        return [
            'news' => !empty($response['news']['contents']) ? $response['news']['contents'] : [],
            'pagination' => $pagination
        ];
    }

    public function findNewsDetailPage(int $id): array
    {
        // 新着情報を取得
        $newsInstance = $this->getNewsDetailInstance($id);

        // データ取得
        $response = $this->findMultiple(['news' => $newsInstance]);

        return [
            'news' => !empty($response['news']['contents']) ? $response['news']['contents'] : [],
            'sibLings' => $response['news']['sibLings']
        ];
    }

    /**
     * 客室情報を取得
     */
    public function findRoomsPage(): array
    {
        // 客室紹介
        $roomInfoInstance = $this->getApiServiceInstance('rooms_info', [
            'token' => API_ROOMS_INFO_TOKEN,
            'params' => ['limit' => 1]
        ]);

        // 客室一覧
        $roomsInstance = $this->getApiServiceInstance('rooms', [
            'token' => API_ROOMS_TOKEN,
            'params' => ['mode' => 'all']
        ]);

        // データ取得
        $response = $this->findMultiple([
            'rooms_info' => $roomInfoInstance,
            'rooms' => $roomsInstance
        ]);

        return [
            'rooms_info' => !empty($response['rooms_info']['contents']) ? $response['rooms_info']['contents'][0] : [],
            'rooms' => !empty($response['rooms']['contents']) ? $response['rooms']['contents'] : []
        ];
    }

    /**
     * アクセス情報を取得
     */
    public function findAccessPage(): array
    {
        // アクセス情報
        $accessInstance = $this->getApiServiceInstance('access', [
            'token' => API_ACCESS_TOKEN,
            'params' => ['limit' => 1]
        ]);

        // データ取得
        $response = $this->findMultiple(['access' => $accessInstance]);

        return [
            'access' => !empty($response['access']['contents']) ? $response['access']['contents'][0] : []
        ];
    }

    public function findFaqPage(): array
    {
        // カテゴリ一覧を取得
        $categoriesInstance = $this->getApiServiceInstance('faq/categories', [
            'token' => API_FAQ_TOKEN,
        ]);

        // FAQ一覧を取得
        $faqInstance = $this->getApiServiceInstance('faq', [
            'token' => API_FAQ_TOKEN,
            'params' => ['mode' => 'all']
        ]);

        // データ取得
        $response = $this->findMultiple([
            'categories' => $categoriesInstance,
            'faq' => $faqInstance
        ]);

        $faqList = $this->formatCategoryToItems(
            !empty($response['categories']['contents']) ? $response['categories']['contents'] : [],
            !empty($response['faq']['contents']) ? $response['faq']['contents'] : []
        );

        return [
            'faq' => $faqList,
        ];
    }
}
