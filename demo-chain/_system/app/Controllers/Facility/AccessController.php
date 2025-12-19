<?php

namespace App\Controllers\Facility;

use App\Services\FacilityService;

class AccessController extends AbstractFacilityController
{

    public function __construct()
    {
        parent::__construct();
        $this->setPageSetting('access');
    }

    public function index()
    {
        $data = (new FacilityService($this->facilityAlias))->findAccessPage();
        return $this->render('_facility/access/index', [
            'cms__access' => $data['access'],
        ]);
    }
}
