<?php

namespace App\Modules\Review\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Settings;

class Review extends BaseController
{
	protected $setting;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
	}

	public function index()
	{
		return view('App\Modules\Review\Views/review', [
			'title' => 'Ulasan ' . lang('App.product'),
		]);
	}
}
