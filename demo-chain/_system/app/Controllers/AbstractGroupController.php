<?php

namespace App\Controllers;

use App\Services\ApiService;
use App\Services\GroupService;

class AbstractGroupController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
        $emergencyNews = (new GroupService())->findEmergencyNews();
        $this->setViewData('cms__emergency_news', $emergencyNews);
    }
}
