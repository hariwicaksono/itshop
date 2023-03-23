<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Modules\Page\Controllers'], function($routes){
	$routes->get('pages', 'Page::index');

});

$routes->group('', ['namespace' => 'App\Modules\Page\Controllers'], function($routes){
	$routes->get('terms', 'Page::page');
	$routes->get('privacy', 'Page::page');
	$routes->get('legal', 'Page::page');
	$routes->get('about', 'Page::page');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Page\Controllers\Api'], function($routes){
	$routes->get('pages', 'Page::index');
	$routes->get('page/(:segment)', 'Page::show/$1');
	$routes->put('page/update/(:segment)', 'Page::update/$1');
	$routes->put('page/setactive/(:segment)', 'Page::setActive/$1');

	
});