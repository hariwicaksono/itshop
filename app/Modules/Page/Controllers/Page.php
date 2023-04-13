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

	public function page()
	{
		$uri = new \CodeIgniter\HTTP\URI(current_url());
		$slug = $uri->getSegment(1);

		$page = $this->page->where('slug', $slug)->first();
		if (session()->get('lang') == 'id') {
			$pageTitle = $page['page_title'];
		} elseif (session()->get('lang') == 'en') {
			$pageTitle = $page['page_title_en'];
		} else {
			$pageTitle = $page['page_title'];
		}
		return view('page', [
			'title' => ucfirst($pageTitle),
			'slug' => $uri->getSegment(1)
		]);
	}

}
