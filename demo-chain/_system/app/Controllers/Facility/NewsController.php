<?php

namespace App\Controllers\Facility;

use App\Services\ApiService;
use App\Services\FacilityService;

class NewsController extends AbstractFacilityController
{

    public function __construct()
    {
        parent::__construct();
        $this->setPageSetting('news');
        $this->setCategories();
    }

    public function index()
    {
        $data = (new FacilityService($this->facilityAlias))->findNewsListPage(10, $_GET);

        return $this->render('_facility/news/index', [
            'cms__news_list' => $data['news'],
            'cms__pagination' => $data['pagination'],
        ]);
    }

    public function detail(mixed $facilityAlias, mixed $id)
    {
        $data = (new FacilityService($this->facilityAlias))->findNewsDetailPage($id);

        return $this->render('_facility/news/detail', [
            'cms__news' => $data['news'],
            'cms__sibLings' => $data['sibLings'],
        ]);
    }

    private function setCategories()
    {
        $data = (new FacilityService($this->facilityAlias))->findCategoriesAndAll($_GET, 'news', API_NEWS_TOKEN);
        $this->view->set('cms__news_categories', ['all' => $data['all'], 'items' => $data['items'], 'active' => $data['active']]);
    }
}
