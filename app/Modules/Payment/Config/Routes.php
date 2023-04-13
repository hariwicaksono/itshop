<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'App\Modules\Payment\Controllers'], function ($routes) {
    $routes->get('payment', 'Payment::index');
    
});

$routes->group('api', ['namespace' => 'App\Modules\Payment\Controllers\Api'], function ($routes) {
	$routes->get('payment', 'Payment::index');
	$routes->get('payment/all', 'Payment::all');
	$routes->get('payment/(:segment)', 'Payment::show/$1');
	$routes->post('payment/save', 'Payment::create');
	$routes->put('payment/update/(:segment)', 'Payment::update/$1');
	$routes->delete('payment/delete/(:segment)', 'Payment::delete/$1');
	$routes->put('payment/setactive/(:segment)', 'Payment::setActive/$1');
	$routes->put('payment/setcod/(:segment)', 'Payment::setCod/$1');
	$routes->get('payment/get/(:segment)', 'Payment::getConfirm/$1');
	$routes->post('payment/confirm', 'Payment::confirm');
});
