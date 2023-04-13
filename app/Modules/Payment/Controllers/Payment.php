<?php

namespace  App\Modules\Payment\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Payment\Models\PaymentModel;

class Payment extends BaseController
{
	protected $setting;
	protected $payment;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
		$this->payment = new PaymentModel();
	}

	public function index()
	{
		return view('App\Modules\Payment\Views/payment', [
			'title' => lang('App.payment'),
		]);
	}

	

}
