<?php

namespace  App\Modules\Page\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Page\Models\PageModel;

class Page extends BaseController
{
	protected $setting;
	protected $terms;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
		$this->page = new PageModel();
	}

	public function index()
	{
		return view('App\Modules\Page\Views/admin_page', [
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

	public function page()
	{
		$uri = new \CodeIgniter\HTTP\URI(current_url());
        $slug = $uri->getSegment(1);
		$page = $this->page->where('slug', $slug)->first();
		//var_dump(session()->get('lang'));die;
		if (session()->get('lang') == NULL || session()->get('lang') == "id") {
			$title = $page['page_title'];
		} else {
			$title = $page['page_title_en'];
		}
		
		return view('App\Modules\Page\Views/page', [
			'title' => $title,
			'slug' => $slug
		]);
	}

}
