<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('openapi', ['namespace' => 'App\Modules\Cart\Controllers\Api'], function ($routes) {
	$routes->get('cart/count', 'Cart::countUserCart');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Cart\Controllers\Api'], function ($routes) {
	$routes->get('cart', 'Cart::index');
	$routes->get('cart/usercart', 'Cart::getUserCart');
	$routes->get('cart/(:segment)', 'Cart::show/$1');
	$routes->post('cart/save', 'Cart::create');
	$routes->put('cart/update/(:segment)', 'Cart::update/$1');
	$routes->delete('cart/delete/(:segment)', 'Cart::delete/$1');
	$routes->get('cart/order/(:segment)', 'Cart::findItem/$1');
	$routes->get('cart/user/orderitem', 'Cart::getOrderItem');
});
