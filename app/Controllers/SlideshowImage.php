<?php namespace App\Controllers;

use App\Models\SlideshowModel;
use \Appkita\CI4Restfull\RestfullApi;

class SlideshowImage extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\SlideshowModel';
    protected $auth = ['key'];

    public function update($id = null)
    {

        if ($this->request)
        {
            //get request from Reactjs
            if($this->request->getJSON()) {
                $input = $this->request->getJSON();
                $data = [
                    'img_slide' => $input->foto
                ];

                if ($data > 0) {
                    $this->model->update($input->id, $data);

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
                
            } /**else {
                //get request from PostMan and more
                $input = $this->request->getRawInput();
                $data = [
                    'img_slide' => $input['foto']
                ];
    
                if ($data > 0) {
                    $this->model->update($id, $data);
    
                    $response = [
                        'status' => '200',
                        'data' => 'Success Update data'
                    ];
                    return $this->respond($response, 200);
                } else {
                    $response = [
                        'status' => '404',
                        'data' => 'Failed Update Data'
                    ];
                    return $this->respond($response, 404);
                }      
            }**/
        }
        
    }


    
}