<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('category', ['filter' => 'auth', 'namespace' => 'App\Modules\Category\Controllers'], function($routes){
	$routes->get('/', 'Category::index');
});

$routes->group('openapi', ['namespace' => 'App\Modules\Category\Controllers\Api'], function($routes){
	$routes->get('category', 'Category::index');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Category\Controllers\Api'], function($routes){
	$routes->get('category', 'Category::index');
	$routes->get('category/(:segment)', 'Category::show/$1');
	$routes->post('category/save', 'Category::create');
	$routes->put('category/update/(:segment)', 'Category::update/$1');
	$routes->delete('category/delete/(:segment)', 'Category::delete/$1');
});