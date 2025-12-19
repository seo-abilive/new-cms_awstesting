<?php

namespace App\Controllers\Facility;

use App\Services\FacilityService;

class FaqController extends AbstractFacilityController
{

    public function __construct()
    {
        parent::__construct();
        $this->setPageSetting('faq');
    }

    public function index()
    {
        $data = (new FacilityService($this->facilityAlias))->findFaqPage();

        return $this->render('_facility/faq/index', [
            'cms__faq' => $data['faq'],
        ]);
    }
}
