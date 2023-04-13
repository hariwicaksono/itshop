<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'App\Modules\Shipment\Controllers'], function ($routes) {
    $routes->get('shipment', 'Shipment::index');
    
});

$routes->group('openapi', ['namespace' => 'App\Modules\Shipment\Controllers\Api'], function ($routes) {
	$routes->get('shipment', 'Shipment::index');
});

$routes->group('api', ['namespace' => 'App\Modules\Shipment\Controllers\Api'], function ($routes) {
	$routes->get('shipment', 'Shipment::index');
	$routes->get('shipment/all', 'Shipment::all');
	$routes->get('shipment/(:segment)', 'Shipment::show/$1');
	$routes->post('shipment/save', 'Shipment::create');
	$routes->put('shipment/update/(:segment)', 'Shipment::update/$1');
	//$routes->delete('shipment/delete/(:segment)', 'Shipment::delete/$1');
	$routes->put('shipment/setactive/(:segment)', 'Shipment::setActive/$1');
});
