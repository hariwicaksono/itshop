<?php namespace App\Controllers;

use App\Models\BlogModel;
use \Appkita\CI4Restfull\RestfullApi;

class BlogImage extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\BlogModel';
    protected $auth = ['key'];
    
    public function update($id = null)
    {
        if ($this->model->find($id)) {

            $input = $this->request->getRawInput();
            $data = [
                'post_image' => $input['foto']
            ];

            if ($this->model->update($id, $data) > 0) {

                $response = [
                    'status' => true,
                    'message' => 'Berhasil memperbarui data',
                    'data' => []
                ];
                return $this->respond($response, 200);
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Gagal memperbarui data',
                    'data' => []
                ];
                return $this->respond($response, 200);
            }

            
        }

        //$response = [
            //'status' => '0',
            //'data' => 'Failed Update Data'
        //];
        //return $this->respond($response, 404);
    }


    
}