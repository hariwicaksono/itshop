<?php

namespace App\Modules\User\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\User\Models\UserModel;

class User extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = UserModel::class;

    public function index()
    {
        $input = $this->request->getVar();
        $role = $input['role'] ?? "";
        if ($role == "") {
            $data = $this->model->findAll();
        } else {
            $data = $this->model->where('role', 2)->orderBy('user_id', 'DESC')->findAll();
        }

        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.noData'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function show($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->findUser($id)], 200);
    }

    public function create()
    {
        $rules = [
            'email' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'username' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'password' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'first_name' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'last_name' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'phone' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'email' => $json->email,
                'username' => $json->username,
                'password' => $json->password,
                'role' => 2,
                'active' => 0,
                'first_name' => $json->first_name,
                'last_name' => $json->last_name,
                'company' => $json->company,
                'phone' => $json->phone,
                'kodepos' => 0
            ];
        } else {
            $data = [
                'email' => $this->request->getPost('email'),
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'role' => 2,
                'active' => 0,
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'company' => $this->request->getPost('company'),
                'phone' => $this->request->getPost('phone'),
                'kodepos' => 0
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->save($data);

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $rules = [
            'username' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'first_name' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'last_name' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'phone' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'email' => $json->email,
                'username' => $json->username,
                'first_name' => $json->first_name,
                'last_name' => $json->last_name,
                'company' => $json->company,
                'phone' => $json->phone,
                'alamat' => $json->alamat,
                'provinsi_id' => $json->provinsi_id,
                'kabupaten_kota_id' => $json->kabupaten_kota_id,
                'kodepos' => $json->kodepos,
                'biography' => $json->biography
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'email' => $input['email'],
                'username' => $input['username'],
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'company' => $input['company'],
                'phone' => $input['phone'],
                'alamat' => $input['alamat'],
                'provinsi_id' => $input['provinsi_id'],
                'kabupaten_kota_id' => $input['kabupaten_kota_id'],
                'kodepos' => $input['kodepos'],
                'biography' => $input['biography']
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $delete = $this->model->find($id);
        $userId = $delete['user_id'];

        if ($userId == 1) :
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        endif;

        if ($delete) {
            $this->model->delete($id);
            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function setActive($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'active' => $json->active
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'active' => $input['active']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);

            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setRole($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'role' => $json->role
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'role' => $input['role']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);

            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function changePassword()
    {
        $rules = [
            'email' => 'required',
            'password' => 'required|min_length[8]|max_length[255]',
            'verify' => 'required|matches[password]'
        ];

        $input = $this->getRequestInput();

        if (!$this->validate($rules)) {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => 'Error',
                    'data' => $this->validator->getErrors()
                ],
                200
            );
        }

        $user = $this->model->where(['email' => $input['email']])->first();
        $user_id = $user['user_id'];
        $user_data = [
            'password' => $input['password'],
        ];
        if ($this->model->update($user_id, $user_data)) {
            return $this->getResponse(
                [
                    'status' => true,
                    'message' => lang('App.passChanged'),
                    'data' => []
                ],
                200
            );
        } else {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => lang('App.regFailed'),
                    'data' => []
                ],
                200
            );
        }
    }
}
