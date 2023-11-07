<?php

namespace App\Modules\Setting\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Setting\Models\SettingModel;

class Setting extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = SettingModel::class;

    public function __construct()
	{
		//memanggil Model
	}

    public function index()
    {
        return $this->respond(["status" => true, "message" => "Success", "data" => $this->model->findAll()], 200);
    }

    public function update($id = NULL)
    {
        $rules = [
            'setting_value' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'setting_value' => $json->setting_value,
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

    public function upload()
    {
        $id = $this->request->getVar('setting_id');
        $image = $this->request->getFile('image');
        $fileName = $image->getRandomName();
        if ($image !== "") {
            $path = "images/";
            $moved = $image->move($path, $fileName);
            if ($moved) {
                $image = \Config\Services::image()
                    ->withFile($path . $fileName)
                    ->resize(64, 64, true, 'height')
                    ->save($path . 'res_' . $fileName );
                    
                $simpan = $this->model->update($id, [
                    'setting_value' => $fileName
                ]);
                if ($simpan) {
                    return $this->respond(["status" => true, "message" => lang('App.imgSuccess'), "data" => [$path . $fileName]], 200);
                } else {
                    return $this->respond(["status" => false, "message" => lang('App.imgFailed'), "data" => []], 200);
                }
            }
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.uploadFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setChange($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'setting_value' => $json->setting_value,
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'setting_value' => $input['setting_value']
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
