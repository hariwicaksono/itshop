<?php namespace App\Controllers;

use App\Models\CommentModel;
use \Appkita\CI4Restfull\RestfullApi;

class CountComment extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\CommentModel';
    protected $auth = ['key'];
    
	public function index()
	{
        $count = $this->model->count_comment();
        $data = [
            'status' => true,
            'message' => 'Berhasil menampilkan data',
            'data' => $count
        ];

        return $this->respond($data, 200);
    }
    
}