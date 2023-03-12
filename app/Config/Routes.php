<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/product/(:segment)', 'Home::show/$1');
$routes->get('/lang/{locale}', 'Home::setLanguage');
$routes->get('/login', 'Auth::login');
$routes->get('/register', 'Auth::register');
$routes->get('/logout', 'Auth::logout');
$routes->get('/verify_email', "Auth::verifyEmail");
$routes->group('password', function ($routes) {
	$routes->get('reset', 'Auth::passwordReset');
	$routes->get('change', 'Auth::passwordChange');
});
//Routes untuk Halaman Keranjang
$routes->group('', ['filter' => 'auth'], function ($routes) {
	$routes->get('cart', 'Member::cart');
	$routes->get('checkout-success', 'Member::checkout');
});
//Routes untuk Halaman admin
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
	$routes->get('/', 'Admin::index');
	$routes->get('order', 'Admin::order');
	$routes->get('product', 'Admin::product');
	$routes->get('payment', 'Admin::payment');
	$routes->get('shipment', 'Admin::shipment');
	$routes->get('setting', 'Admin::setting');
	$routes->get('export', 'Admin::export');
	$routes->get('export-tcpdf', 'Admin::exportTcpdf');
	$routes->get('export-mpdf', 'Admin::exportMpdf');
	$routes->get('export-html2pdf', 'Admin::exportHtml2pdf');
	$routes->get('export-excel', 'Admin::exportExcel');
	$routes->get('user', 'Admin::user');
});
//Routes untuk Halaman member/user
$routes->group('member', ['filter' => 'auth'], function ($routes) {
	$routes->get('/', 'Member::index');
	$routes->get('dashboard', 'Member::index');
	$routes->get('order-list', 'Member::order');
	$routes->get('profile', 'Member::profile');
});
$routes->group('auth', ['namespace' => $routes->getDefaultNamespace() . 'Api'], function ($routes) {
	$routes->post('login', 'Auth::login');
	$routes->post('register', 'Auth::register');
	$routes->post('resetPassword', 'Auth::resetPassword');
	$routes->post('changePassword', 'Auth::changePassword');
});
//Contoh Routes untuk RESTful Api
$routes->group('api', ['filter' => 'jwtauth', 'namespace' => $routes->getDefaultNamespace() . 'Api'], function ($routes) {
	$routes->get('product', 'Product::index');
	$routes->get('product/all', 'Product::allProduct');
	$routes->get('product/(:segment)', 'Product::show/$1');
	$routes->post('product/save', 'Product::create');
	$routes->put('product/update/(:segment)', 'Product::update/$1');
	$routes->delete('product/delete/(:segment)', 'Product::delete/$1');
	$routes->put('product/setactive/(:segment)', 'Product::setActive/$1');
	$routes->put('product/setstock/(:segment)', 'Product::setStock/$1');
	$routes->put('product/setprice/(:segment)', 'Product::setPrice/$1');

	$routes->post('media/save', 'Media::create');
	$routes->delete('media/delete/(:segment)', 'Media::delete/$1');

	$routes->get('user', 'User::index');
	$routes->get('user/(:segment)', 'User::show/$1');
	$routes->put('user/update/(:segment)', 'User::update/$1');
	$routes->delete('user/delete/(:segment)', 'User::delete/$1');
	$routes->put('user/setactive/(:segment)', 'User::setActive/$1');
	$routes->put('user/setrole/(:segment)', 'User::setRole/$1');

	$routes->get('cart', 'Cart::index');
	$routes->get('cart/usercart', 'Cart::getUserCart');
	$routes->get('cart/(:segment)', 'Cart::show/$1');
	$routes->post('cart/save', 'Cart::create');
	$routes->put('cart/update/(:segment)', 'Cart::update/$1');
	$routes->delete('cart/delete/(:segment)', 'Cart::delete/$1');
	$routes->get('cart/order/(:segment)', 'Cart::findItem/$1');

	$routes->get('order', 'Order::index');
	$routes->post('order/save', 'Order::create');
	$routes->put('order/update/(:segment)', 'Order::update/$1');
	$routes->get('order/(:segment)', 'Order::show/$1');
	$routes->put('order/setstatus/(:segment)', 'Order::setStatus/$1');
	$routes->get('order/user/(:segment)', 'Order::getUserOrder/$1');
	$routes->get('order/pending/(:segment)', 'Order::getUserOrderPending/$1');
	$routes->get('order/delivered/(:segment)', 'Order::getUserOrderDelivered/$1');
	$routes->get('order/canceled/(:segment)', 'Order::getUserOrderCanceled/$1');
	$routes->get('chart1', 'Order::chart1');

	$routes->get('payment', 'Payment::index');
	$routes->get('payment/all', 'Payment::all');
	$routes->get('payment/(:segment)', 'Payment::show/$1');
	$routes->post('payment/save', 'Payment::create');
	$routes->put('payment/update/(:segment)', 'Payment::update/$1');
	$routes->delete('payment/delete/(:segment)', 'Payment::delete/$1');
	$routes->put('payment/setactive/(:segment)', 'Payment::setActive/$1');
	$routes->put('payment/setcod/(:segment)', 'Payment::setCod/$1');
	$routes->get('payment/get/(:segment)', 'Payment::getConfirm/$1');
	$routes->post('payment/confirm', 'Payment::confirm');

	$routes->get('shipment', 'Shipment::index');
	$routes->get('shipment/all', 'Shipment::all');
	$routes->get('shipment/(:segment)', 'Shipment::show/$1');
	$routes->post('shipment/save', 'Shipment::create');
	$routes->put('shipment/update/(:segment)', 'Shipment::update/$1');
	//$routes->delete('shipment/delete/(:segment)', 'Shipment::delete/$1');
	$routes->put('shipment/setactive/(:segment)', 'Shipment::setActive/$1');

	$routes->get('setting', 'Setting::index');
	$routes->put('setting/update/(:segment)', 'Setting::update/$1');

	$routes->get('kabupaten', 'Kabupaten::index');
	$routes->get('kabupaten/get', 'Kabupaten::getProvinsi');
	$routes->get('provinsi', 'Provinsi::index');
});
//Contoh Routes untuk Open Api
$routes->group('openapi', ['namespace' => $routes->getDefaultNamespace() . 'Api'], function ($routes) {
	$routes->get('product/all', 'Product::allProduct');
	$routes->get('product/(:segment)', 'Product::show/$1');
	$routes->get('cart/count', 'Cart::countUserCart');
});

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
