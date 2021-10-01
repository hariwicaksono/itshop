<?php namespace App\Controllers;

use App\Models\BlogModel;
use \Appkita\CI4Restfull\RestfullApi;

class Tag extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\BlogModel';
    protected $auth = ['key'];

    public function index()
    {
        $id=$this->request->getVar('category');

        if ($id == null) {
			return $this->respond(['status' => false,'message'=> 'id tidak boleh kosong','data' => []]);
		} else {
			$data = $this->model->searchTag($id);
		}
		
        if ($data) {
            $response = [
                'status' => true,
                'message' => 'Berhasil menampilkan semua data',
                'data' => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => 'Tidak ada data',
            ];
            return $this->respond($response, 200);
        }
    }

}