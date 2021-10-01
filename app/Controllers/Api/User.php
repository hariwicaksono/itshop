<?php namespace App\Controllers;

use App\Models\UserModel;
use \Appkita\CI4Restfull\RestfullApi;

class User extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\UserModel';
    protected $auth = ['key'];

	public function index()
	{
        $id = $this->request->getVar('id');
        if ($id == null) {
			$user = $this->model->getUser();
		} else {
			$user = $this->model->getUser($id);
		}

        if ($user) {
            $response = [
                'status' => true,
                'message' => 'Berhasil menampilkan semua data',
                'data' => $user
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
            'data' => $this->model->getUser($id),
            //'data' => $this->model->find($id)
        ];

        return $this->respond($data, 200);
    }

    public function create()
    {
        
        $data = [
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'name' => $this->request->getPost('name'),
            'status_user' => $this->request->getPost('status_user'),
            'status_active' => $this->request->getPost('status_active'),
            'created_at' => date("Y-m-d H:i:s")
        ];

        if ($this->model->save($data) > 0) {
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
 
    public function update($id = null)
    {
        if ($this->request)
        {
            //get request from Reactjs
            if($this->request->getJSON()) {
                $input = $this->request->getJSON();
                $data = [
                    'email' => $input->email,
                    'name' => $input->name,
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
                
            } /**else {
                //get request from PostMan and more
                $input = $this->request->getRawInput();
                $data = [
                    'email' => $input['email'],
                    'name' => $input['name'],
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

    public function delete($id = null)
    {
        $id = $this->model->find($id);
        if ($id) {
                $this->model->delete($id);
                $response = [
                    'status' => true,
                    'message' => 'Berhasil menghapus data',
                    'data' => []
                ];
                return $this->respond($response, 200);
        }  else {
                $response = [
                    'status' => false,
                    'message' => 'Gagal menghapus data',
                    'data' => []
                ];
                return $this->respond($response, 200);
        }  
    }
    
}