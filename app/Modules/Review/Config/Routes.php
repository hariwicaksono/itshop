<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// Web route (admin moderation page)
$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Modules\Review\Controllers'], function ($routes) {
    $routes->get('reviews', 'Review::index');
});

// Public API: get approved reviews + ratings per product (no auth required)
$routes->group('api', ['namespace' => 'App\Modules\Review\Controllers\Api'], function ($routes) {
    $routes->get('home/review/product/(:segment)', 'Review::getByProduct/$1');
    $routes->get('home/review/rating/(:segment)', 'Review::getRating/$1');
    $routes->get('home/review/ratings-batch', 'Review::getRatingsBatch');
    $routes->post('home/review/save', 'Review::create');
});

// Authenticated API: full CRUD + moderation
$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Review\Controllers\Api'], function ($routes) {
    $routes->get('review', 'Review::index');
    $routes->get('review/(:segment)', 'Review::show/$1');
    $routes->post('review/save', 'Review::create');
    $routes->put('review/update/(:segment)', 'Review::update/$1');
    $routes->delete('review/delete/(:segment)', 'Review::delete/$1');
    $routes->put('review/setstatus/(:segment)', 'Review::setStatus/$1');
    $routes->post('review/generate-ai-reviews', 'Review::generateAIReviews');
});
