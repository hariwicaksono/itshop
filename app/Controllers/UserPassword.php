<?php namespace App\Controllers;

use App\Models\UserModel;
use \Appkita\CI4Restfull\RestfullApi;

class UserPassword extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\UserModel';
    protected $auth = ['key'];
    
    public function update($id = null)
    {
        
        if ($this->request)
        {
            //get request from Reactjs
            if($this->request->getJSON()) {
                $input = $this->request->getJSON();
                $data = [
                    'password' => md5($input->password)
                ];

                if ($data > 0) {
                    $this->model->updatePassword($data, $input->id);

                    $response = [
                        'status' => true,
                        'message' => 'Berhasil memperbarui password',
                        'data' => []
                    ];
                    return $this->respond($response, 200);
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal memperbarui password',
                        'data' => []
                    ];
                    return $this->respond($response, 200);
                }
                
            } /**else {
                //get request from PostMan and more
                $input = $this->request->getRawInput();
                $data = [
                    'password' => md5($input['password'])
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