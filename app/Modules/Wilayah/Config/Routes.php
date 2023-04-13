<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'App\Modules\Wilayah\Controllers'], function ($routes) {
    $routes->get('wilayah', 'Wilayah::index');
    
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Wilayah\Controllers\Api'], function ($routes) {
	$routes->get('kabupaten', 'Kabupaten::index');
	$routes->get('kabupaten/get', 'Kabupaten::getProvinsi');
	$routes->get('provinsi', 'Provinsi::index');
});
