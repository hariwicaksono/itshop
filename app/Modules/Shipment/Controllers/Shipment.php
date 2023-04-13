<?php

namespace  App\Modules\Shipment\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Shipment\Models\ShipmentModel;

class Shipment extends BaseController
{
	protected $setting;
	protected $shipment;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
		$this->shipment = new ShipmentModel();
	}

	public function index()
	{
		return view('App\Modules\Shipment\Views/shipment', [
			'title' => lang('App.shipment'),
		]);
	}

	

}
