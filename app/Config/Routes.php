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
 * HMVC Routing
 * --------------------------------------------------------------------
 */

foreach(glob(APPPATH . 'Modules/*', GLOB_ONLYDIR) as $item_dir)
{
	if (file_exists($item_dir . '/Config/Routes.php'))
	{
		require_once($item_dir . '/Config/Routes.php');
	}	
}

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/source-code/(:segment)', 'Home::show/$1');
$routes->get('/lang/{locale}', 'Home::setLanguage');

//Routes untuk Halaman Keranjang
$routes->group('', ['filter' => 'auth'], function ($routes) {
	$routes->get('cart', 'Member::cart');
	$routes->get('checkout', 'Member::checkoutProcess');
	$routes->get('checkout/success/pending', 'Member::checkoutTFSuccess');
	$routes->get('checkout/success/finish', 'Member::checkoutPGSuccess');
});

//Routes untuk Halaman admin
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
	$routes->get('/', 'Admin::index');
	$routes->get('export', 'Admin::export');
	$routes->get('export-tcpdf', 'Admin::exportTcpdf');
	$routes->get('export-mpdf', 'Admin::exportMpdf');
	$routes->get('export-html2pdf', 'Admin::exportHtml2pdf');
	$routes->get('export-excel', 'Admin::exportExcel');
});

//Routes untuk Halaman member/user
$routes->group('member', ['filter' => 'auth'], function ($routes) {
	$routes->get('/', 'Member::index');
	$routes->get('dashboard', 'Member::index');
	$routes->get('order-list', 'Member::order');
	$routes->get('profile', 'Member::profile');
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
