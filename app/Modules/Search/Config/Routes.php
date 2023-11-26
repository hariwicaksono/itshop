<?php

if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('api', ['namespace' => 'App\Modules\Search\Controllers\Api'], function ($routes) {
	$routes->get('search', 'Search::index');
});
