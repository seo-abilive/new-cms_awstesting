<?php

namespace App\Controllers;

class ContactController extends AbstractGroupController
{
    public function index()
    {
        return $this->render('contact/index');
    }
}
