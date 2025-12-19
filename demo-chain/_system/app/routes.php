<?php

/**
 * @var Core\Router $router
 * ルート設定
 */

use App\Controllers\Facility\AccessController;
use App\Controllers\Facility\RoomsController;
use App\Controllers\TopController;
use App\Controllers\Facility\TopController as FacilityTopController;
use App\Controllers\HotelController;
use App\Controllers\NewsController;
use App\Controllers\FaqController;
use App\Controllers\Facility\FaqController as FacilityFaqController;
use App\Controllers\ContactController;
use App\Controllers\WebhookController;
use App\Controllers\Facility\NewsController as FacilityNewsController;

// 総合サイト
$router->get('/', [TopController::class, 'index']);
$router->get('/hotel/', [HotelController::class, 'index']);
$router->get('/news/', [NewsController::class, 'index']);
$router->get('/news/:id/', [NewsController::class, 'detail']);
$router->get('/faq/', [FaqController::class, 'index']);
$router->get('/contact/', [ContactController::class, 'index']);

// 施設サイト
$router->get('/:facility_alias/', [FacilityTopController::class, 'index']);
$router->get('/:facility_alias/rooms/', [RoomsController::class, 'index']);
$router->get('/:facility_alias/access/', [AccessController::class, 'index']);
$router->get('/:facility_alias/faq/', [FacilityFaqController::class, 'index']);
$router->get('/:facility_alias/news/', [FacilityNewsController::class, 'index']);
$router->get('/:facility_alias/news/:id/', [FacilityNewsController::class, 'detail']);

// WebHook
$router->post('/webhook/cache/clear', [WebhookController::class, 'clearCache']);
