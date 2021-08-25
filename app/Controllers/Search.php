<?php namespace App\Controllers;

use App\Models\BlogModel;
use \Appkita\CI4Restfull\RestfullApi;

class Search extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\BlogModel';
    protected $auth = ['key'];
    
    public function index()
    {
        $id=$this->request->getVar('id');

        if ($id == null) {
			return $this->respond(['status' => false,'message'=> 'id tidak boleh kosong','data'=>[]]);
		} else {
			$search = $this->model->searchBlog($id);
		}
		
        if ($search) {
            $response = [
                'status' => true,
                'message' => 'Berhasil menampilkan data',
                'data' => $search
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => 'Tidak ada data',
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

}