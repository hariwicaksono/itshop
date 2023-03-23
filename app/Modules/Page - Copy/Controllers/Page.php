<?php

namespace  App\Modules\Page\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Page\Models\PageModel;

class Page extends BaseController
{
	protected $setting;
	protected $page;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
		$this->page = new PageModel();
	}

	public function index()
	{
		return view('App\Modules\Page\Views/page', [
			'title' => 'Pages',
		]);
	}

	public function show($id = null)
	{
		$find = $this->page->where('slug', $id)->first();
		$idPage = $find['page_id'];
		$page = $this->page->showPage($idPage);
		$pageName = $page['page_name'];
		//var_dump($sold);die;
		return view('product', [
			'title' => $pageName,
			'page_id' => $idPage,
		]);
	}

	public function terms()
	{
		return view('App\Modules\Page\Views/terms', [
			'title' => 'Terms and Conditions',
		]);
	}

}
