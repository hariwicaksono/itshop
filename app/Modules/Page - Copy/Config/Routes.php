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
});