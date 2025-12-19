<?php

namespace App\Controllers;

use App\Services\GroupService;

class FaqController extends AbstractGroupController
{
    public function index()
    {
        $data = (new GroupService())->findFaqListPage($_GET);

        return $this->render('faq/index', [
            'cms__faq' => $data['faq'],
            'cms__markup_data' => $data['markup'],
        ]);
    }
}
