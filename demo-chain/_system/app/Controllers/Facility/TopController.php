<?php

namespace App\Controllers\Facility;

use App\Services\FacilityService;

class TopController extends AbstractFacilityController
{
    public function index()
    {
        $data = (new FacilityService($this->facilityAlias))->findTopPage();

        return $this->render('_facility/index', [
            'cms__top_page_setting' => $data['top_page_setting'],
            'cms__top_banner_list' => $data['top_banner'],
            'cms__news_list' => $data['news'],
        ]);
    }
}
