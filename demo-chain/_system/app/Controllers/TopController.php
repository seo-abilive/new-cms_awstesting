<?php

namespace App\Controllers;

use App\Services\GroupService;

class TopController extends AbstractGroupController
{

    public function index()
    {
        $data = (new GroupService())->findTopPage();

        return $this->render('index', [
            'cms__banner_list' => $data['top_banner'],
            'cms__news_list' => $data['news'],
        ]);
    }
}
