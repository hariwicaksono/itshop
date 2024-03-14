<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Media\Controllers\Api'], function ($routes) {
    $routes->post('media/save', 'Media::create');
    $routes->post('media/save_article', 'Media::create2');
    $routes->delete('media/delete/(:segment)', 'Media::delete/$1');
});
