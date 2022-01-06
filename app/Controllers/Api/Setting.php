<?php

namespace App\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Models\SettingModel;

class Setting extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = SettingModel::class;

    public function index()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->find(1)], 200);
    }

    public function update($id = NULL)
    {
        $rules = [
            'site_title' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'site_title' => $json->site_title,
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
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }
}
