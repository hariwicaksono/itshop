<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('', ['namespace' => 'App\Modules\Article\Controllers'], function($routes){
	$routes->get('(:segment)/(:segment)/(:segment)/(:segment)', 'Article::article/$1');
});

$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Modules\Article\Controllers'], function($routes){
	$routes->get('articles', 'Article::index');
});

$routes->group('openapi', ['namespace' => 'App\Modules\Article\Controllers\Api'], function($routes){
	$routes->get('articles', 'Article::index');
	$routes->get('articles/all', 'Article::allArticles');
	$routes->get('article/(:segment)', 'Article::show/$1');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Article\Controllers\Api'], function($routes){
	$routes->get('articles', 'Article::index');
	$routes->get('article/(:segment)', 'Article::show/$1');
	$routes->post('article/save', 'Article::create');
	$routes->put('article/update/(:segment)', 'Article::update/$1');
	$routes->put('article/setactive/(:segment)', 'Article::setActive/$1');
	$routes->delete('article/delete/(:segment)', 'Article::delete/$1');
	
});