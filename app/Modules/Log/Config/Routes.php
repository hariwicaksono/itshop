<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('log', ['filter' => 'auth', 'namespace' => 'App\Modules\Log\Controllers'], function ($routes) {
    // Akun
    $routes->get('/', 'Log::index');

});
