<?php namespace App\Controllers;

use App\Models\SettingModel;
use \Appkita\CI4Restfull\RestfullApi;

class Setting extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\SettingModel';
    protected $auth = ['key'];

	public function index()
	{
        $id = '1';
        $data = [
            'status' => true,
            'message' => 'Berhasil menampilkan data',
            'data' => $this->model->getSetting($id)
        ];

        return $this->respond($data, 200);
    }
    
    public function show($id = null)
    {
        $data = [
            'status' => true,
            'message' => 'Berhasil menampilkan data',
            'data' => $this->model->find($id)
        ];

        return $this->respond($data, 200);
    }

    public function update($id = null)
    {
        if ($this->request)
        {
            //get request from Reactjs
            if($this->request->getJSON()) {
                $input = $this->request->getJSON();
                $data = [
                    'company' => $input->company,
                    'website' => $input->website,
                    'phone' => $input->phone,
                    'email' => $input->email,
                    'theme' => $input->theme,
                    'updated_at' => date("Y-m-d H:i:s")
                ];

                if ($data > 0) {
                    $this->model->update($input->id, $data);

                    $response = [
                        'status' => true,
                        'message' => 'Berhasil menyimpan data',
                        'data' => []
                    ];
                    return $this->respond($response, 200);
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal menyimpan data',
                        'data' => []
                    ];
                    return $this->respond($response, 200);
                }
                
            } /**else {
                //get request from PostMan and more
                $input = $this->request->getRawInput();
                $data = [
                    'company' => $input['company'],
                    'website' => $input['website'],
                    'phone' => $input['phone'],
                    'email' => $input['email'],
                    'theme' => $input['theme'],
                    'updated_at' => date("Y-m-d H:i:s")
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