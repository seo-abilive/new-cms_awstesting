<?php

namespace App\Controllers;

use App\Services\ApiService;

class TopController extends AbstractController
{
    public function index()
    {
        // 新着情報を取得
        $newsList = (new ApiService('news'))
            ->enableCache(60)
            ->params(['limit' => 3])
            ->setToken(API_NEWS_TOKEN)
            ->addFacility('demo')
            ->getList();

        // TOPバナーを取得
        $bannerList = (new ApiService('top_banner'))
            ->enableCache(60)
            ->params(['mode' => 'all'])
            ->setToken(API_TOP_BANNER_TOKEN)
            ->addFacility('demo')
            ->getList();

        // メインテンプレートをレンダリング（View変数も渡す）
        return $this->render('index', [
            'cms__news_list' => $newsList ? $newsList['contents'] : [],
            'cms__banner_list' => $bannerList ? $bannerList['contents'] : []
        ]);
    }
}
