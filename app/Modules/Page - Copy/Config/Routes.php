<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Modules\Page\Controllers'], function($routes){
	$routes->get('pages', 'Page::index');
});

$routes->group('', ['namespace' => 'App\Modules\Page\Controllers'], function($routes){
	$routes->get('terms', 'Page::terms');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Page\Controllers\Api'], function($routes){
    $routes->get('pages', 'Page::index');
	$routes->put('setting/update/(:segment)', 'Setting::update/$1');
	$routes->post('setting/upload', 'Setting::upload');

	$routes->put('setting/change/(:segment)', 'Setting::setChange/$1');

	$routes->get('setting/provinsi', 'Setting::provinsi');
	$routes->get('setting/kota', 'Setting::kota');
	$routes->get('setting/kota/get', 'Setting::getKota');
	$routes->get('setting/layout', 'Setting::layout');
	
});