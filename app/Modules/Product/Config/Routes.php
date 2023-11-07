<?php

if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Modules\Product\Controllers'], function ($routes) {
	$routes->get('products', 'Product::index');
});

$routes->group('openapi', ['namespace' => 'App\Modules\Product\Controllers\Api'], function ($routes) {
	$routes->get('product/all', 'Product::allProduct');
	$routes->get('product/(:segment)', 'Product::show/$1');
});


$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Product\Controllers\Api'], function ($routes) {
	$routes->get('product', 'Product::index');
	$routes->get('product/all', 'Product::allProduct');
	$routes->get('product/(:segment)', 'Product::show/$1');
	$routes->post('product/save', 'Product::create');
	$routes->put('product/update/(:segment)', 'Product::update/$1');
	$routes->delete('product/delete/(:segment)', 'Product::delete/$1');
	$routes->put('product/setactive/(:segment)', 'Product::setActive/$1');
	$routes->put('product/setstock/(:segment)', 'Product::setStock/$1');
	$routes->put('product/setprice/(:segment)', 'Product::setPrice/$1');
	$routes->get('product/sold/best_seller', 'Product::bestSeller');
	$routes->post('product/total', 'Product::total');
});
