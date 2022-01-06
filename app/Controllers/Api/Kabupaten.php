<?php

namespace App\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Models\KabupatenModel;

class Kabupaten extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = KabupatenModel::class;

    public function index()
    {
        $data = $this->model->findAll();
        if ($data) {
            $response = [
                'status' => true,
                'message' => lang('App.successGetData'),
                'data' => $data,
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

    public function getProvinsi()
    {
        $input = $this->request->getVar();
        $select = $input['provinsi'];

        $data = $this->model->where(['provinsi_id' => $select])->findAll();

        if ($data) {
            $response = [
                'status' => true,
                'message' => lang('App.getSuccess'),
                'data' => $data,
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
}
