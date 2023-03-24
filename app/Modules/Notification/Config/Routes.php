<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('notification', ['namespace' => 'App\Modules\Notification\Controllers'], function($routes){
	$routes->post('payment', 'Notification::index');
});
