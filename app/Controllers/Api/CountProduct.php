<?php namespace App\Controllers;

use App\Models\ProductModel;
use \Appkita\CI4Restfull\RestfullApi;

class CountProduct extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\ProductModel';
    protected $auth = ['key'];

	public function index()
	{
        $count = $this->model->count_product();
        $data = [
            'status' => true,
            'message' => 'Berhasil menampilkan data',
            'data' => $count
        ];

        return $this->respond($data, 200);
    }
    
}