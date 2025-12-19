<?php

namespace App\Controllers\Facility;

use App\Services\FacilityService;

class RoomsController extends AbstractFacilityController
{
    public function __construct()
    {
        parent::__construct();
        $this->setPageSetting('rooms');
    }

    public function index()
    {
        $data = (new FacilityService($this->facilityAlias))->findRoomsPage();

        return $this->render('_facility/rooms/index', [
            'cms__rooms_info' => $data['rooms_info'],
            'cms__rooms_list' => $data['rooms'],
        ]);
    }
}
