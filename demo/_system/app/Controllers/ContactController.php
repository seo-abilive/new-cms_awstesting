<?php
namespace App\Controllers;

class ContactController extends AbstractController
{
    public function index()
    {
        $this->render('contact/index');
    }
}