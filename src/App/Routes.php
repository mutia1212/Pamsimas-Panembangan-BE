<?php

declare(strict_types=1);

use Slim\Routing\RouteCollectorProxy;

use App\Controller\{
    MasterData
};
use App\Controller\Admin\{
    MasterAdmin,
    MasterStaff,
    MasterCustomer,
    SettingWaterPrice,
    Transaction
};

/* ------------------------------ General Routes ------------------------------ */

// Start Route
$app->get('/', 'App\Controller\Hello:getStatusAPI')->setName('main');

// Authentication Routes
$app->post('/auth/signin', 'App\Controller\Auth:signin')->setName('signin');

// Users Routes
$app->group('/user', function (RouteCollectorProxy $group) {
    $group->get('/info', 'App\Controller\User:info');
    $group->get('/profile', 'App\Controller\User:profile');
    $group->post('/profile/save', 'App\Controller\User:save');
    $group->post('/profile/change_password', 'App\Controller\User:change_password');
});

function routes($app, $url, $controller): void {
    $app->group("/$url", function (RouteCollectorProxy $group) use($controller) {
        $group->get('', [$controller, 'index']);
        $group->post('/save', [$controller, 'save']);
        $group->get('/{id}', [$controller, 'detail']);
        $group->delete('/bulk-delete', [$controller, 'bulkDelete']);
        $group->delete('[/{id}]', [$controller, 'delete']);
    });
}

// Diagnostic Data
$app->get('/diagnostic', 'App\Controller\Hello:getDiagnostic')->setName('diagnostic');

// Test Connect Database
$app->get('/testgetdata', 'App\Controller\Hello:testConnectFetchData');

$app->group('/master-data', function (RouteCollectorProxy $group) {
    $group->get('/list-category', [MasterData::class, 'listCategory']);
});
/* --------------------------------------------------------------------------- */

/* ------------------------------ Super Admin Routes ------------------------------ */
routes($app, 'admin', MasterAdmin::class);
routes($app, 'staff', MasterStaff::class);
routes($app, 'customer', MasterCustomer::class);
routes($app, 'setting-water-price', SettingWaterPrice::class);
routes($app, 'transaction', Transaction::class);