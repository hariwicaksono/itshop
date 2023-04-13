<?php

namespace App\Modules\Wilayah\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Wilayah\Models\ProvinsiModel;

class Provinsi extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = ProvinsiModel::class;

    public function index()
    {
        $data = $this->model->findAll();
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