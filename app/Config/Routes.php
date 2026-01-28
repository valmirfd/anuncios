<?php

use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', [HomeController::class, 'index'], ['as' => 'home']);

$routes->group('dashboard', static function ($routes) {
    $routes->get('/', [DashboardController::class, 'index'], ['as' => 'dashboard']);

   
});
