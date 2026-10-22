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
		// Build SEO description from body (strip HTML tags)
		$pageBody = '';
		if (session()->get('lang') == 'en') {
			$pageBody = $page['page_body_en'] ?: $page['page_body'];
		} else {
			$pageBody = $page['page_body'];
		}
		$seoDescription = substr(strip_tags($pageBody), 0, 160);

		return view('page', [
			'title' => ucfirst($pageTitle),
			'slug' => $uri->getSegment(1),
			'page' => $page,
			'page_description' => $seoDescription,
			'page_id' => $page['page_id'],
			'page_publish_date' => $page['created_at'] ?? date('Y-m-d H:i:s'),
			'page_update_date' => $page['updated_at'] ?? $page['created_at'] ?? date('Y-m-d H:i:s'),
		]);
	}
}
