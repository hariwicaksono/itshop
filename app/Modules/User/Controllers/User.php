<?php

namespace  App\Modules\User\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\User\Models\UserModel;

class User extends BaseController
{
	protected $setting;
	protected $user;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
		$this->user = new UserModel();
	}

	public function index()
	{
		return view('App\Modules\User\Views/user', [
			'title' => 'Users',
		]);
	}

	

}
