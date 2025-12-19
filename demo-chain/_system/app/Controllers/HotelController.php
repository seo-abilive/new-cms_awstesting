<?php

namespace App\Controllers;

use App\Services\GroupService;

class HotelController extends AbstractGroupController
{
    public function index()
    {
        $data = (new GroupService())->findHotelListPage();

        return $this->render('hotel/index', [
            'cms__hotel_list' => $data['hotel'],
        ]);
    }
}
