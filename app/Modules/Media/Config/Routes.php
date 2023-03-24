<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Media\Controllers\Api'], function ($routes) {
    $routes->post('media/save', 'Media::create');
    $routes->delete('media/delete/(:segment)', 'Media::delete/$1');
});
