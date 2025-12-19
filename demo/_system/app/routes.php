<?php

use App\Controllers\ContactController;
use App\Controllers\FaqController;
use App\Controllers\TopController;
use App\Controllers\NewsController;

$router->get('/', [TopController::class, 'index']);
$router->get('/news/', [NewsController::class, 'index']);
$router->post('/news/preview', [NewsController::class, 'preview']);
$router->get('/news/:id', [NewsController::class, 'detail']);
$router->get('/faq/', [FaqController::class, 'index']);
$router->get('/contact/', [ContactController::class, 'index']);
