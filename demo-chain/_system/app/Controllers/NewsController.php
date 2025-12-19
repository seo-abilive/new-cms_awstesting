<?php

namespace App\Controllers;

use App\Services\ApiService;
use App\Services\GroupService;
use Core\Pagination;

class NewsController extends AbstractGroupController
{

    public function __construct()
    {
        parent::__construct();
        $this->setCategories();
    }

    public function index()
    {
        $data = (new GroupService())->findNewsListPage(10, $_GET);

        return $this->render('news/index', [
            'cms__news_list' => $data['news'],
            'cms__pagination' => $data['pagination'],
            'cms__markup_data' => $data['markup'],
        ]);
    }

    public function detail(mixed $id)
    {
        $data = (new GroupService())->findNewsDetailPage($id);

        return $this->render('news/detail', [
            'cms__news' => $data['news'],
            'cms__sibLings' => $data['sibLings'],
            'cms__markup_data' => $data['markup'],
        ]);
    }

    protected function setCategories()
    {
        $data = (new GroupService())->findCategoriesAndAll($_GET, 'news', API_NEWS_TOKEN);
        $this->view->set('cms__news_categories', [
            'all' => $data['all'],
            'items' => $data['items'],
            'active' => $data['active']
        ]);
    }
}
