<?php

use CodeIgniter\Router\RouteCollection;
use App\Modules\Category\Models\CategoryModel;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/lang/{locale}', 'Home::setLanguage');

//Routes Detail Produk tampil sesuai Nama Category
$catModel = new CategoryModel();
$category = $catModel->findAll();
foreach ($category as $row) {
	$routes->get('/' . $row['category_slug'] . '/(:segment)', 'Home::show/$1');
}

//Routes untuk Halaman Keranjang
$routes->group('', ['filter' => 'auth'], function ($routes) {
	$routes->get('cart', 'Member::cart');
	$routes->get('checkout', 'Member::checkoutProcess');
	$routes->get('checkout/success/pending', 'Member::checkoutTFSuccess');
	$routes->get('checkout/success/finish', 'Member::checkoutPGSuccess');
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
 * HMVC Routing
 * --------------------------------------------------------------------
 */

foreach (glob(APPPATH . 'Modules/*', GLOB_ONLYDIR) as $item_dir) {
	if (file_exists($item_dir . '/Config/Routes.php')) {
		require_once($item_dir . '/Config/Routes.php');
	}
}
