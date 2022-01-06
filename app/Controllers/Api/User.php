<?php

namespace App\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Models\UserModel;

class User extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = UserModel::class;

    public function index()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->findAll()], 200);
    }

    public function show($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->findUser($id)], 200);
    }
    
    public function update($id = NULL)
    {
        $rules = [
            'username' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nama_lengkap' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'username' => $json->username,
                'nama_lengkap' => $json->nama_lengkap,
                'alamat' => $json->alamat,
                'provinsi_id' => $json->provinsi_id,
                'kabupaten_id' => $json->kabupaten_id,
                'kodepos' => $json->kodepos,
            ];
        } else {
            $data = $this->request->getRawInput();
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {

            $simpan = $this->model->update($id, $data);
            if ($simpan) {
                $response = [
                    'status' => true,
                    'message' => lang('App.updSuccess'),
                    'data' => [],
                ];
                return $this->respond($response, 200);
            }
        }
    }

    public function delete($id = null)
    {
        $hapus = $this->model->find($id);
        if ($hapus) {
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
}
