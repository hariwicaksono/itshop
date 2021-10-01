<?php namespace App\Controllers;

use App\Models\CategoryModel;
use \Appkita\CI4Restfull\RestfullApi;

class CountCategory extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\CategoryModel';
    protected $auth = ['key'];

	public function index()
	{
        $count = $this->model->count_category();
        $data = [
            'status' => true,
            'message' => 'Berhasil menampilkan data',
            'data' => $count
        ];

        return $this->respond($data, 200);
    }
    
}