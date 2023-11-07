<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

//Routes untuk Halaman admin
$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Modules\Admin\Controllers'], function ($routes) {
	$routes->get('/', 'Admin::index');
	$routes->get('export', 'Admin::export');
	$routes->get('export-tcpdf', 'Admin::exportTcpdf');
	$routes->get('export-mpdf', 'Admin::exportMpdf');
	$routes->get('export-html2pdf', 'Admin::exportHtml2pdf');
	$routes->get('export-excel', 'Admin::exportExcel');
});