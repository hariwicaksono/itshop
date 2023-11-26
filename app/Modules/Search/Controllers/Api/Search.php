<?php

namespace App\Modules\Search\Controllers\Api;
/*
PT ITSHOP BISNIS DIGITAL
Website: https://itshop.biz.id
Toko Online: ITSHOP Purwokerto (Tokopedia.com/itshoppwt, Shopee.co.id/itshoppwt, Bukalapak.com/itshoppwt)
Dibuat oleh: Hari Wicaksono, S.Kom
Created: 11-2023
Modified: 11-2023
*/

use App\Controllers\BaseControllerApi;
use App\Modules\Product\Models\ProductModel;

class Search extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = ProductModel::class;


    public function __construct()
    {
        //memanggil Model

    }

    public function index()
    {
        $input = $this->request->getVar();
        $keyword = $input['keyword'];
        $data = $this->model->searchData($keyword);
        //var_dump($this->model->getLastQuery()->getQuery());
        //die;
        if ($data) {
            $response = [
                'status' => true,
                'message' => lang('App.getSuccess'),
                'data' => $data
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
