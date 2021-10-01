<?php namespace App\Controllers;

use App\Models\CommentModel;
use \Appkita\CI4Restfull\RestfullApi;
 
class Comment extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\CommentModel';
    protected $auth = ['key'];
	public function index()
	{
        $id=$this->request->getVar('id');

        if ($id == null) {
			$data = $this->model->getComment();
		} else {
			$data = $this->model->getComment($id);
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
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }
    
    public function show($id = null)
    {
        $data = [
            'status' => true,
            'message' => 'Berhasil menampilkan data',
            'data' => $this->model->getComment($id)
        ];

        return $this->respond($data, 200);
    }

    public function create()
    {
        if ($this->request)
        {
            //get request from Reactjs
            if($this->request->getJSON()) {
                $input = $this->request->getJSON();
                $data = [
                    'post_id' => $input->post_id,
                    'name' => $input->name,
                    'email' => $input->email,
                    'body' => $input->body,
                    'active' => '',
                    'created_at' => date("Y-m-d H:i:s")
                ];

                if ($data > 0) {
                    $this->model->save($data);
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
            }
            else { 
                $response = [
                    'status' => false,
                    'message' => 'Metode tidak diizinkan',
                    'data' => []
                ];
                return $this->respond($response, 200);
                }
        }
    }

    public function update($id = null)
    {
        if ($this->request)
        {
            //get request from Reactjs
            if($this->request->getJSON()) {
                $input = $this->request->getJSON();
                $data = [
                    'active' => $input->active,
                    'updated_at' => date("Y-m-d H:i:s")
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
                
            } 
            else { 
                $response = [
                    'status' => false,
                    'message' => 'Metode tidak diizinkan',
                    'data' => []
                ];
                return $this->respond($response, 200);
                }
            /**else {
                //get request from PostMan and more
                $input = $this->request->getRawInput();
                $data = [
                    'active' => $input['active'],
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