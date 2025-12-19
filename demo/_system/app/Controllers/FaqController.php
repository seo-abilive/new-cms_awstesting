<?php

namespace App\Controllers;

use App\Services\ApiService;

class FaqController extends AbstractController
{
    public function index()
    {
        // カテゴリを取得
        $categories = (new ApiService('faq'))
            ->enableCache(60)
            ->setToken(API_FAQ_TOKEN)
            ->addFacility('demo')
            ->getCategories();

        // FAQを取得
        $items = (new ApiService('faq'))
            ->enableCache(60)
            ->setToken(API_FAQ_TOKEN)
            ->params(['mode' => 'all'])
            ->addFacility('demo')
            ->getList();

        // カテゴリとFAQを結合
        $faqList = [];
        if (!empty($categories['contents'])) {

            foreach ($categories['contents'] as $category) {

                // カテゴリのFAQを取得
                $cateItems = array_filter($items['contents'] ?? [], function ($item) use ($category) {
                    $cateIds = array_map(function ($cate) {
                        return $cate['id'];
                    }, ($item['categories'] ?? []));
                    return in_array($category['id'], $cateIds);
                });

                $faqList[] = [
                    'category' => $category,
                    'faqs' => $cateItems
                ];
            }
        }

        $markupData = (new ApiService('faq'))
            ->enableCache(60)
            ->setToken(API_FAQ_TOKEN)
            ->addFacility('demo')
            ->params(['mode' => 'all'])
            ->getMarkupList('faq');

        return $this->render('faq/index', [
            'cms__faq' => $faqList,
            'cms__markup_data' => $markupData
        ]);
    }
}
