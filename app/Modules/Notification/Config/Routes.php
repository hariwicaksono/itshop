<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('notification', ['filter' => 'auth', 'namespace' => 'App\Modules\Notification\Controllers'], function($routes){
	$routes->post('handling', 'Notification::index');
});
