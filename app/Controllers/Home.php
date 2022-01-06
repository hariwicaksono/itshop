<?php namespace App\Controllers;

use App\Models\SettingModel;

class Home extends BaseController
{
	public function __construct()
    {
        //memanggil Model
        $this->settingModel = new SettingModel();
		$this->setting = $this->settingModel->find(1);
    }

	public function index()
	{
		return view('home', [
            'title' => $this->setting['site_title'],
			'description' => $this->setting['site_description']
        ]);
	}

	public function setLanguage()
	{
		$lang = $this->request->uri->getSegments()[1];
		$this->session->set("lang", $lang);
		return redirect()->to(base_url());
	}

	//--------------------------------------------------------------------

}
