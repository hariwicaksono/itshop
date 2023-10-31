<?php

namespace App\Controllers;

use App\Modules\Product\Models\ProductModel;
use App\Libraries\Settings;

class Home extends BaseController
{
	protected $product;
	protected $setting;

	function __construct()
	{
		$this->product = new ProductModel();
		$this->setting = new Settings();
	}

	public function index()
	{
		return view('home', [
			'title' => $this->setting->info['title_home'],
			'app_name' => $this->setting->info['app_name'],
			'telepon' => $this->setting->info['company_telepon'],
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
		]);
	}

	public function setLanguage()
	{
		$lang = $this->request->uri->getSegments()[1];
		$this->session->set("lang", $lang);
		return redirect()->back()->with('success', 'Language successfully changed to ' . $lang);
	}

	//--------------------------------------------------------------------

}
