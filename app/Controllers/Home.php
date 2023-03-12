<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Home extends BaseController
{
	protected $product;

	function __construct()
	{
		$this->product = new ProductModel();
	}

	public function index()
	{
		return view('home');
	}

	public function show($id = null)
	{
		$find = $this->product->where('slug', $id)->first();
		$idProduct = $find['product_id'];
		$product = $this->product->showProduct($idProduct);
		$productName = $product['product_name'];
		//var_dump($productName);die;
		return view('product', [
            'title' => $productName,
			'product_id' => $idProduct
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
