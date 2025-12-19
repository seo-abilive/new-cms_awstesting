<?php

namespace App\Controllers\Facility;

use App\Controllers\AbstractController;
use App\Services\ApiService;
use App\Services\FacilityService;

abstract class AbstractFacilityController extends AbstractController
{
    protected ?string $facilityAlias = null;

    public function __construct()
    {
        parent::__construct();
        // ルーターからfacility_aliasを取得
        $facilityAlias = $this->router->getParam('facility_alias');
        if ($facilityAlias !== null) {
            $this->facilityAlias = $facilityAlias;
            $this->setViewData('cms__facility_alias', $facilityAlias);

            $data = (new FacilityService($this->facilityAlias))->findCommon();

            $this->setViewData('cms__emergency_news', $data['emergency']);
            $this->setViewData('cms__facility_setting', $data['facility_setting']);
        } else {
            abort(404, 'Facility not found');
        }
    }

    protected function setPageSetting($targetPage)
    {
        $data = (new FacilityService($this->facilityAlias))->findPageSetting($targetPage);
        $this->setViewData('cms__page_setting', $data);
    }
}
