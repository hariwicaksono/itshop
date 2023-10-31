<?php

if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Modules\Order\Controllers'], function ($routes) {
	$routes->get('order', 'Order::index');
});

$routes->group('openapi', ['namespace' => 'App\Modules\Order\Controllers\Api'], function ($routes) {
	$routes->get('order/count/pending_processed', 'Order::countUserOrder');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Order\Controllers\Api'], function ($routes) {
	$routes->get('order', 'Order::index');
	$routes->post('order/save', 'Order::create');
	$routes->put('order/update/(:segment)', 'Order::update/$1');
	$routes->get('order/(:segment)', 'Order::show/$1');
	$routes->put('order/setstatus/(:segment)', 'Order::setStatus/$1');
	$routes->put('order/setstatus/payment/(:segment)', 'Order::setStatusPayment/$1');
	$routes->get('order/user/(:segment)', 'Order::getUserOrder/$1');
	$routes->get('order/pending/(:segment)', 'Order::getUserOrderPending/$1');
	$routes->get('order/processed/(:segment)', 'Order::getUserOrderProcessed/$1');
	$routes->get('order/delivered/(:segment)', 'Order::getUserOrderDelivered/$1');
	$routes->get('order/canceled/(:segment)', 'Order::getUserOrderCanceled/$1');
	$routes->get('chart1', 'Order::chart1');
	$routes->get('order/count/new_order', 'Order::countNewOrder');
	$routes->get('order/count/pending_processed', 'Order::countUserOrder');
	$routes->put('order/update_link_gdrive/(:segment)', 'Order::updateLinkGdrive/$1');
});
