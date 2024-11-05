<?php

namespace App\Controllers;

use App\Modules\Product\Models\ProductModel;
use App\Libraries\Settings;
use App\Modules\Article\Models\ArticleModel;
use App\Modules\Page\Models\PageModel;

class Home extends BaseController
{
	protected $product;
	protected $setting;
	protected $page;
	protected $article;

	function __construct()
	{
		if (!session()->get('lang')) :
			session()->set('lang', env('app.defaultLocale'));
		endif;
		
		$this->product = new ProductModel();
		$this->setting = new Settings();
		$this->page = new PageModel();
		$this->article = new ArticleModel();
	}

	public function index(): string
	{
		return view('home', [
			'title' => $this->setting->info['title_home'],
			'app_name' => $this->setting->info['app_name'],
			'company_name' => $this->setting->info['company_nama'],
			'telepon' => $this->setting->info['company_telepon'],
			'wa_text' => $this->setting->info['wa_text'],
		]);
	}

	public function show($id = null)
	{
		$find = $this->product->where('slug', $id)->first();
		$idProduct = $find['product_id'];
		$product = $this->product->showProduct($idProduct);
		$productName = $product['product_name'];
		$sold = $this->product->countProductSold($idProduct, 2);
		//var_dump($sold);die;
		return view('product', [
			'title' => $productName,
			'product_id' => $idProduct,
			'productSold' => $sold,
			'app_name' => $this->setting->info['app_name'],
			'telepon' => $this->setting->info['company_telepon'],
			'wa_text' => $this->setting->info['wa_text'],
			'product' => $product
		]);
	}

	public function setLanguage()
	{
		$lang = $this->request->uri->getSegments()[1];
		$this->session->set("lang", $lang);
		return redirect()->back()->with('success', 'Language successfully changed to ' . $lang);
	}

	public function sitemap()
	{
		$this->response->setHeader('Content-Type', 'text/xml;charset=UTF-8');
		return view('sitemap', [
			'title' => 'Sitemap',
			'pages' => $this->page->orderBy('page_id', 'DESC')->findAll(),
			'products' => $this->product->getProduct(),
			'articles' => $this->article->getArticles()
		]);
	}

	//--------------------------------------------------------------------

}
