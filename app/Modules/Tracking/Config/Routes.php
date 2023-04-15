<?php

if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

/* $routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Modules\Tracking\Controllers'], function ($routes) {
	$routes->get('order', 'Tracking::index');
}); */

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Tracking\Controllers\Api'], function ($routes) {
	$routes->get('tracking/(:segment)', 'Tracking::index/$1');
	$routes->post('tracking/save', 'Tracking::create');
});
